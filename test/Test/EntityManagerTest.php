<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test;

use Williarin\WordpressInterop\Repository\EntityRepository;
use Williarin\WordpressInterop\Test\Fixture\Entity\Foo;
use Williarin\WordpressInterop\Test\Fixture\Repository\FooRepository;

class EntityManagerTest extends TestCase
{
    public function testGetRepositoryReturnsCorrectRepository(): void
    {
        $this->manager->addRepository(new Fixture\Repository\FooRepository($this->manager, $this->serializer));
        self::assertInstanceOf(FooRepository::class, $this->manager->getRepository(Foo::class));
    }

    public function testGetRepositoryReturnsDefaultEntityRepository(): void
    {
        $repository = $this->manager->getRepository(Foo::class);
        self::assertInstanceOf(EntityRepository::class, $repository);
        self::assertNotInstanceOf(FooRepository::class, $repository);
    }
}
