<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Fixture\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Repository\AbstractEntityRepository;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Test\Fixture\Entity\Bar;

final class BarRepository extends AbstractEntityRepository
{
    public function __construct(protected EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        parent::__construct($entityManager, $serializer, Bar::class);
    }

    public function getBarTerms(): array
    {
        return ['blue', 'green', 'red'];
    }
}
