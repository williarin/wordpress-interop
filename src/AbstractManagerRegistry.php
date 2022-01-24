<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use InvalidArgumentException;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;

abstract class AbstractManagerRegistry implements ManagerRegistryInterface
{
    public function __construct(
        private array $managers,
        private string $defaultManager
    ) {
    }

    public function getDefaultManagerName(): string
    {
        return $this->defaultManager;
    }

    public function getManager(string $name = null): EntityManagerInterface
    {
        if ($name === null) {
            $name = $this->defaultManager;
        }

        if (!isset($this->managers[$name])) {
            throw new InvalidArgumentException(sprintf('Wordpress Manager named "%s" does not exist.', $name));
        }

        return $this->getService($this->managers[$name]);
    }

    /**
     * @return EntityManagerInterface[]
     */
    public function getManagers(): array
    {
        $services = [];

        foreach ($this->managers as $name => $id) {
            $services[$name] = $this->getService($id);
        }

        return $services;
    }

    /**
     * @return string[]
     */
    public function getManagerNames(): array
    {
        return $this->managers;
    }

    public function getRepository(string $entityClassName, ?string $managerName = null): RepositoryInterface
    {
        return $this
            ->getManager($managerName)
            ->getRepository($entityClassName)
        ;
    }

    abstract protected function getService(string $name): EntityManagerInterface;
}
