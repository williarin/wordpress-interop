<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Fixture\Repository;

use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Repository\EntityRepository;
use Williarin\WordpressInterop\Test\Fixture\Entity\Foo;

final class FooRepository extends EntityRepository
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, Foo::class);
    }

    public function getFooTerms(): array
    {
        return ['brown', 'black'];
    }
}
