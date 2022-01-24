<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Repository\OptionRepository;
use Williarin\WordpressInterop\Test\TestCase;

class OptionRepositoryTest extends TestCase
{
    private OptionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new OptionRepository($this->manager, $this->serializer);
    }

    public function testFindByNameReturnsCorrectValue(): void
    {
        self::assertEquals('My Awesome WordPress Site', $this->repository->find('blogname'));
    }

    public function testFindByNameUnserializesValue(): void
    {
        $value = $this->repository->find('wp_user_roles');
        self::assertIsArray($value);
        self::assertArrayHasKey('administrator', $value);
    }

    public function testFindByNameReturnsNullIfNotFound(): void
    {
        self::assertNull($this->repository->find('nonexistent_option'));
    }
}
