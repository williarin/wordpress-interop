<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Doctrine\DBAL\Connection;
use ReflectionClass;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Persistence\DuplicationService;
use Williarin\WordpressInterop\Persistence\DuplicationServiceInterface;

abstract class AbstractEntityManager implements EntityManagerInterface
{
    private array $repositories = [];
    private ?DuplicationServiceInterface $duplicationService;

    public function __construct(
        private Connection $connection,
        protected SerializerInterface $serializer,
        private string $tablePrefix = 'wp_',
        DuplicationServiceInterface $duplicationService = null,
    ) {
        $this->duplicationService = $duplicationService;
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

    public function getDuplicationService(): DuplicationServiceInterface
    {
        if (!$this->duplicationService) {
            $this->duplicationService = new DuplicationService(new AsciiSlugger());
        }

        $this->duplicationService->setEntityManager($this);

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
