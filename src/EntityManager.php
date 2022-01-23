<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Doctrine\DBAL\Connection;
use ReflectionClass;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Repository\EntityRepository;
use Williarin\WordpressInterop\Repository\RepositoryInterface;

final class EntityManager implements EntityManagerInterface
{
    private array $repositories = [];

    public function __construct(
        private Connection $connection,
        private SerializerInterface $serializer,
        private string $tablePrefix = 'wp_'
    ) {
    }

    public static function create(
        Connection $connection,
        SerializerInterface $serializer,
        string $tablePrefix = 'wp_'
    ): EntityManager {
        return new EntityManager($connection, $serializer, $tablePrefix);
    }

    public function addRepository(RepositoryInterface $repository): EntityManagerInterface
    {
        $this->repositories[$repository->getEntityClassName()] = $repository;

        return $this;
    }

    /**
     * @return RepositoryInterface[]
     */
    public function getRepositories(): array
    {
        return $this->repositories;
    }

    public function getRepository(string $entityClassName): RepositoryInterface
    {
        if (empty($this->repositories[$entityClassName])) {
            $this->repositories[$entityClassName] = $this->createRepositoryForClass($entityClassName);
        }

        return $this->repositories[$entityClassName];
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getTablesPrefix(): string
    {
        return $this->tablePrefix;
    }

    private function createRepositoryForClass(string $entityClassName): RepositoryInterface
    {
        if (class_exists($entityClassName)) {
            $reflectionClass = new ReflectionClass($entityClassName);

            if ($attribute = current($reflectionClass->getAttributes(RepositoryClass::class))) {
                $repositoryClassName = $attribute->newInstance()
                    ->className;

                return new $repositoryClassName($this, $this->serializer, $entityClassName);
            }
        }

        return new EntityRepository($this, $this->serializer, $entityClassName);
    }
}
