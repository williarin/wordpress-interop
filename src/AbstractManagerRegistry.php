<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Persistence\DuplicationServiceInterface;

abstract class AbstractManagerRegistry implements ManagerRegistryInterface
{
    private ServiceContainer $container;

    public function __construct(
        private array $managers,
        private string $defaultManager
    ) {
        $this->container = new ServiceContainer();
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

    /**
     * @deprecated Since 1.12.0, use get(DuplicationServiceInterface::class) instead. Will be removed in 2.0
     */
    public function getDuplicationService(?string $managerName = null): DuplicationServiceInterface
    {
        return $this
            ->getManager($managerName)
            ->getDuplicationService()
        ;
    }

    public function get(string $serviceId, ?string $managerName = null): ?object
    {
        $service = $this->container->get($serviceId);

        if ($service && (new \ReflectionClass($service))->hasMethod('setEntityManager')) {
            $service->setEntityManager($this->getManager($managerName));
        }

        return $service;
    }

    abstract protected function getService(string $name): EntityManagerInterface;
}
