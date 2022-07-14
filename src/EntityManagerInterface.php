<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Doctrine\DBAL\Connection;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Persistence\DuplicationServiceInterface;

interface EntityManagerInterface
{
    public function getConnection(): Connection;

    public function addRepository(RepositoryInterface $repository): self;

    public function getRepositories(): array;

    public function getRepository(string $entityClassName): RepositoryInterface;

    public function getTablesPrefix(): string;

    public function getDuplicationService(): DuplicationServiceInterface;
}
