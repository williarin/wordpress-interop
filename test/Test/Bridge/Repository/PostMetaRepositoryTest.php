<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Repository\PostMetaRepository;
use Williarin\WordpressInterop\Test\TestCase;

class PostMetaRepositoryTest extends TestCase
{
    private PostMetaRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PostMetaRepository($this->manager, $this->serializer);
    }

    public function testFindValueByKeyReturnsCorrectValue(): void
    {
        self::assertEquals('value3', $this->repository->findValueByKey(5, 'key1'));
    }

    public function testFindValueByKeyUnserializesValue(): void
    {
        $value = $this->repository->findValueByKey(7, '_wp_attachment_metadata');
        self::assertIsArray($value);
        self::assertArrayHasKey('width', $value);
    }

    public function testFindValueByKeyReturnsNullIfNotFound(): void
    {
        self::assertNull($this->repository->findValueByKey(5, 'nonexistent_key'));
    }
}
