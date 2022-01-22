<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Fixture\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Repository\EntityRepository;
use Williarin\WordpressInterop\Test\Fixture\Entity\Foo;

final class FooRepository extends EntityRepository
{
    public function __construct(protected EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        parent::__construct($entityManager, $serializer, Foo::class);
    }

    public function getFooTerms(): array
    {
        return ['brown', 'black'];
    }
}
