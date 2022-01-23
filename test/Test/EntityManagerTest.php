<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test;

use Williarin\WordpressInterop\Repository\EntityRepository;
use Williarin\WordpressInterop\Test\Fixture\Entity\Bar;
use Williarin\WordpressInterop\Test\Fixture\Entity\Foo;
use Williarin\WordpressInterop\Test\Fixture\Repository\BarRepository;
use Williarin\WordpressInterop\Test\Fixture\Repository\FooRepository;

class EntityManagerTest extends TestCase
{
    public function testRepositoryIsFoundForGivenEntity(): void
    {
        $repository = $this->manager->getRepository(Foo::class);
        self::assertInstanceOf(FooRepository::class, $repository);
    }

    public function testRepositoryIsNotDuplicated(): void
    {
        $repository1 = $this->manager->getRepository(Foo::class);
        $repository2 = $this->manager->getRepository(Foo::class);
        self::assertSame($repository1, $repository2);
    }

    public function testGetRepositoryReturnsDefaultEntityRepository(): void
    {
        $repository = $this->manager->getRepository(Bar::class);
        self::assertInstanceOf(EntityRepository::class, $repository);
        self::assertNotInstanceOf(BarRepository::class, $repository);
    }
}
