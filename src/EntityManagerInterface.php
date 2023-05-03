<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Doctrine\DBAL\Connection;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Persistence\DuplicationServiceInterface;

interface EntityManagerInterface
{
    public function getConnection(): Connection;

    public function addRepository(RepositoryInterface $repository): self;

    public function getRepositories(): array;

    public function getRepository(string $entityClassName): RepositoryInterface;

    public function getTablesPrefix(): string;

    /**
     * @deprecated Since 1.12.0, use DuplicationService::create($this) instead. Will be removed in 2.0
     */
    public function getDuplicationService(): DuplicationServiceInterface;

    public function persist(BaseEntity $entity): void;
}
