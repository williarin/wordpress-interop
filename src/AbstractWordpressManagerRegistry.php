<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use InvalidArgumentException;

abstract class AbstractWordpressManagerRegistry implements WordpressManagerRegistryInterface
{
    public function __construct(private array $managers, private string $defaultManager)
    {
    }

    abstract protected function getService(string $name): WordpressManagerInterface;

    public function getDefaultManagerName(): string
    {
        return $this->defaultManager;
    }

    public function getManager(string $name = null): WordpressManagerInterface
    {
        if ($name === null) {
            $name = $this->defaultManager;
        }

        if (!isset($this->managers[$name])) {
            throw new InvalidArgumentException(sprintf('Wordpress Manager named "%s" does not exist.', $name));
        }

        return $this->getService($this->managers[$name]);
    }

    public function getManagers(): array
    {
        $services = [];

        foreach ($this->managers as $name => $id) {
            $services[$name] = $this->getService($id);
        }

        return $services;
    }

    public function getManagerNames(): array
    {
        return $this->managers;
    }
}
