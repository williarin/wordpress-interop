<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Doctrine\DBAL\Connection;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Repository\EntityRepository;
use Williarin\WordpressInterop\Repository\RepositoryInterface;

final class EntityManager implements EntityManagerInterface
{
    private array $repositories = [];

    public function __construct(private Connection $connection, private SerializerInterface $serializer, private string $tablePrefix = 'wp_')
    {
    }

    public function addRepository(RepositoryInterface $repository): EntityManagerInterface
    {
        $this->repositories[$repository->getEntityClassName()] = $repository;

        return $this;
    }

    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function getRepository(string $entityClassName): RepositoryInterface
    {
        return $this->repositories[$entityClassName] ?? new EntityRepository($this, $this->serializer, $entityClassName);
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getTablesPrefix(): string
    {
        return $this->tablePrefix;
    }
}
