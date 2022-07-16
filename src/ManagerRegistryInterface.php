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

    public function getDuplicationService(?string $managerName = null): DuplicationServiceInterface;
}
