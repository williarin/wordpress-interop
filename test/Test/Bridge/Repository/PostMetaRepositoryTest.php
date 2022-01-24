<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Repository\PostMetaRepository;
use Williarin\WordpressInterop\Exception\PostMetaKeyAlreadyExistsException;
use Williarin\WordpressInterop\Exception\PostMetaKeyNotFoundException;
use Williarin\WordpressInterop\Test\TestCase;

class PostMetaRepositoryTest extends TestCase
{
    private PostMetaRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PostMetaRepository($this->manager, $this->serializer);
    }

    public function testFindReturnsCorrectValue(): void
    {
        self::assertEquals('value3', $this->repository->find(11, 'key1'));
    }

    public function testFindUnserializesValue(): void
    {
        $value = $this->repository->find(13, '_wp_attachment_metadata');
        self::assertIsArray($value);
        self::assertArrayHasKey('width', $value);
    }

    public function testFindThrowsExceptionIfNotFound(): void
    {
        $this->expectException(PostMetaKeyNotFoundException::class);
        $this->repository->find(5, 'nonexistent_key');
    }

    public function testCreateNewStringValue(): void
    {
        $this->repository->delete(5, 'new_key');
        $result = $this->repository->create(5, 'new_key', 'hello');
        self::assertTrue($result);
        self::assertEquals('hello', $this->repository->find(5, 'new_key'));
    }

    public function testCreateNewArrayValue(): void
    {
        $this->repository->delete(5, 'new_serialized_key');
        $result = $this->repository->create(5, 'new_serialized_key', ['hello' => 'world']);
        self::assertTrue($result);
        self::assertEquals('a:1:{s:5:"hello";s:5:"world";}', $this->repository->find(5, 'new_serialized_key', false));
    }

    public function testCantCreateDuplicates(): void
    {
        $this->repository->delete(5, 'about_to_be_duplicated');
        $this->repository->create(5, 'about_to_be_duplicated', 'hello');

        $this->expectException(PostMetaKeyAlreadyExistsException::class);
        $this->repository->create(5, 'about_to_be_duplicated', 'world');
    }

    public function testUpdateExistingValueWithStringValue(): void
    {
        $this->repository->delete(5, 'new_unique_key');
        $this->repository->create(5, 'new_unique_key', 'hello');
        $result = $this->repository->update(5, 'new_unique_key', 'world');
        self::assertTrue($result);
        self::assertEquals('world', $this->repository->find(5, 'new_unique_key'));
    }

    public function testUpdateExistingValueWithArrayValue(): void
    {
        $this->repository->delete(5, 'new_unique_serialized_key');
        $this->repository->create(5, 'new_unique_serialized_key', 'hello');
        $result = $this->repository->update(5, 'new_unique_serialized_key', ['this_is_an' => 'array']);
        self::assertTrue($result);
        self::assertEquals('a:1:{s:10:"this_is_an";s:5:"array";}', $this->repository->find(5, 'new_unique_serialized_key', false));
    }

    public function testUpdateNonExistentKeyReturnsFalse(): void
    {
        self::assertFalse($this->repository->update(5555, 'nonexistent_key', 'world'));
    }

    public function testDeletePostMetaWorks(): void
    {
        $this->repository->delete(5, 'a_key_to_delete');
        $this->repository->create(5, 'a_key_to_delete', 'hello');
        $result = $this->repository->delete(5, 'a_key_to_delete');
        self::assertTrue($result);

        $this->expectException(PostMetaKeyNotFoundException::class);
        $this->repository->find(5, 'a_key_to_delete');
    }

    public function testDeleteNonExistentKeyReturnsFalse(): void
    {
        self::assertFalse($this->repository->update(4444, 'another_nonexistent_key', 'hello'));
    }
}
