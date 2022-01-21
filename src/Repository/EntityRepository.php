<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Repository;

use Williarin\WordpressInterop\EntityManagerInterface;

class EntityRepository implements RepositoryInterface
{
    public function __construct(protected EntityManagerInterface $entityManager, protected string $entityClass)
    {
    }
}
