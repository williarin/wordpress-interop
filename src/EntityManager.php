<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Doctrine\DBAL\Connection;
use Williarin\WordpressInterop\Provider\WordpressProviderInterface;
use Williarin\WordpressInterop\Repository\EntityRepository;
use Williarin\WordpressInterop\Repository\RepositoryFactoryInterface;
use Williarin\WordpressInterop\Repository\RepositoryInterface;

final class EntityManager implements EntityManagerInterface
{
    private array $repositories = [];

    public function __construct(private Connection $connection)
    {
    }

    public function addRepository(RepositoryInterface $repository, string $entityClassName): EntityManagerInterface
    {
        $this->repositories[$entityClassName] = $repository;

        return $this;
    }

    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function getRepository(string $entityClassName): RepositoryInterface
    {
        return $this->repositories[$entityClassName] ?? new EntityRepository($this, $entityClassName);
    }
}
