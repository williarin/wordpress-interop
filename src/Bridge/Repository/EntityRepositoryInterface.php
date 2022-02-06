<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;

interface EntityRepositoryInterface extends RepositoryInterface
{
    public function find(int $id): BaseEntity;

    public function findOneBy(array $criteria, array $orderBy = null): BaseEntity;

    public function findAll(array $orderBy = null): array;

    public function findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null): array;

    public function updateSingleField(int $id, string $field, mixed $newValue): bool;
}
