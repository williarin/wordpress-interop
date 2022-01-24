<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Repository\OptionRepository;
use Williarin\WordpressInterop\Exception\OptionNotFoundException;
use Williarin\WordpressInterop\Test\TestCase;

class OptionRepositoryTest extends TestCase
{
    private OptionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new OptionRepository($this->manager, $this->serializer);
    }

    public function testFindReturnsCorrectValue(): void
    {
        self::assertEquals('My Awesome WordPress Site', $this->repository->find('blogname'));
    }

    public function testFindUnserializesValue(): void
    {
        $value = $this->repository->find('wp_user_roles');
        self::assertIsArray($value);
        self::assertArrayHasKey('administrator', $value);
    }

    public function testFindThrowsExceptionIfNotFound(): void
    {
        $this->expectException(OptionNotFoundException::class);
        $this->repository->find('nonexistent_option');
    }

    public function testCallMagicGetterReturnsOptionValue(): void
    {
        self::assertEquals('My Awesome WordPress Site', $this->repository->getBlogName());
    }

    public function testCallMagicGetterReturnsArrayIfDataIsSerialized(): void
    {
        self::assertIsArray($this->repository->getActivePlugins());
    }

    public function testCallMagicGetterForNonExistentOptionThrowsException(): void
    {
        $this->expectException(OptionNotFoundException::class);
        $this->repository->getSomeNonExistentOption();
    }
}
