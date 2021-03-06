<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Doctrine\DBAL\Query\QueryBuilder;

interface EntityRepositoryInterface extends RepositoryInterface
{
    public function setOptions(array $options): self;

    public function find(int $id): mixed;

    public function findOneBy(array $criteria, array $orderBy = null): mixed;

    public function findAll(array $orderBy = null): array;

    public function findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null): array;

    public function createFindByQueryBuilder(array $criteria, ?array $orderBy): QueryBuilder;

    public function updateSingleField(int $id, string $field, mixed $newValue): bool;

    public function persist(mixed $entity): void;

    public function getMetaEntityClassName(): string;
}
