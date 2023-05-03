<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Doctrine\DBAL\Connection;
use JetBrains\PhpStorm\Deprecated;
use ReflectionClass;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Persistence\DuplicationService;
use Williarin\WordpressInterop\Persistence\DuplicationServiceInterface;

abstract class AbstractEntityManager implements EntityManagerInterface
{
    private array $repositories = [];

    #[Deprecated]
    private ?DuplicationServiceInterface $duplicationService = null;

    public function __construct(
        private Connection $connection,
        protected SerializerInterface $serializer,
        private string $tablePrefix = 'wp_',
        #[Deprecated]
        DuplicationServiceInterface $duplicationService = null,
    ) {
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
            $this->repositories[$entityClassName] = $this->getRepositoryServiceForClass($entityClassName);
        }

        $this->repositories[$entityClassName]->setEntityManager($this);

        return $this->repositories[$entityClassName];
    }

    /**
     * @deprecated Since 1.12.0, use DuplicationService::create($this) instead. Will be removed in 2.0
     */
    public function getDuplicationService(): DuplicationServiceInterface
    {
        if (!$this->duplicationService) {
            $this->duplicationService = DuplicationService::create($this);
        }

        return $this->duplicationService;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function getTablesPrefix(): string
    {
        return $this->tablePrefix;
    }

    public function persist(BaseEntity $entity): void
    {
        $this->getRepository($entity::class)->persist($entity);
    }

    abstract protected function getRepositoryServiceForClass(?string $entityClassName): RepositoryInterface;

    protected function getRepositoryNameForClass(string $entityClassName): ?string
    {
        if (class_exists($entityClassName)) {
            $reflectionClass = new ReflectionClass($entityClassName);

            if ($attribute = current($reflectionClass->getAttributes(RepositoryClass::class))) {
                return $attribute->newInstance()
                    ->className;
            }
        }

        return null;
    }
}
