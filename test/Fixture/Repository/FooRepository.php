<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Fixture\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Repository\AbstractEntityRepository;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Test\Fixture\Entity\Foo;

final class FooRepository extends AbstractEntityRepository
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
