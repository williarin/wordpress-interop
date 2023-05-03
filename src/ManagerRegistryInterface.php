<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Persistence\DuplicationServiceInterface;

interface ManagerRegistryInterface
{
    public function getDefaultManagerName(): string;

    public function getManager(string $name = null): EntityManagerInterface;

    /**
     * @return EntityManagerInterface[]
     */
    public function getManagers(): array;

    /**
     * @return string[]
     */
    public function getManagerNames(): array;

    public function getRepository(string $entityClassName, ?string $managerName = null): RepositoryInterface;

    /**
     * @deprecated Since 1.12.0, use get(DuplicationServiceInterface::class) instead. Will be removed in 2.0
     */
    public function getDuplicationService(?string $managerName = null): DuplicationServiceInterface;

    /**
     * Get a service from the dedicated service container
     */
    public function get(string $serviceId, ?string $managerName = null): ?object;
}
