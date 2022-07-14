<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Option;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
use Williarin\WordpressInterop\Exception\OptionAlreadyExistsException;
use Williarin\WordpressInterop\Exception\OptionNotFoundException;
use Williarin\WordpressInterop\Test\TestCase;

class OptionRepositoryTest extends TestCase
{
    private RepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(Option::class);
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

    public function testCallMagicGetterForNonExistentMethodThrowsException(): void
    {
        $this->expectException(MethodNotFoundException::class);
        $this->repository->someNonExistentMethod();
    }

    public function testGetEntityClassName(): void
    {
        self::assertSame(Option::class, $this->repository->getEntityClassName());
    }

    public function testCreateNewStringValue(): void
    {
        $this->repository->delete('new_option');
        $result = $this->repository->create('new_option', 'hello');
        self::assertTrue($result);
        self::assertEquals('hello', $this->repository->find('new_option'));
    }

    public function testCreateNewArrayValue(): void
    {
        $this->repository->delete('new_serialized_option');
        $result = $this->repository->create('new_serialized_option', ['hello' => 'world']);
        self::assertTrue($result);
        self::assertEquals('a:1:{s:5:"hello";s:5:"world";}', $this->repository->find('new_serialized_option', false));
    }

    public function testCantCreateDuplicates(): void
    {
        $this->repository->delete('about_to_be_duplicated');
        $this->repository->create('about_to_be_duplicated', 'hello');

        $this->expectException(OptionAlreadyExistsException::class);
        $this->repository->create('about_to_be_duplicated', 'world');
    }

    public function testUpdateExistingValueWithStringValue(): void
    {
        $this->repository->delete('new_unique_option');
        $this->repository->create('new_unique_option', 'hello');
        $result = $this->repository->update('new_unique_option', 'world');
        self::assertTrue($result);
        self::assertEquals('world', $this->repository->find('new_unique_option'));
    }

    public function testUpdateExistingValueWithArrayValue(): void
    {
        $this->repository->delete('new_unique_serialized_option');
        $this->repository->create('new_unique_serialized_option', 'hello');
        $result = $this->repository->update('new_unique_serialized_option', ['this_is_an' => 'array']);
        self::assertTrue($result);
        self::assertEquals('a:1:{s:10:"this_is_an";s:5:"array";}', $this->repository->find('new_unique_serialized_option', false));
    }

    public function testUpdateNonExistentOptionReturnsFalse(): void
    {
        self::assertFalse($this->repository->update('nonexistent_option', 'world'));
    }

    public function testUpdateNonExistentOptionThrowsException(): void
    {
        $this->expectException(OptionNotFoundException::class);
        $this->repository->update('nonexistent_option', 'world', true);
    }

    public function testDeleteOptionWorks(): void
    {
        $this->repository->delete('an_option_to_delete');
        $this->repository->create('an_option_to_delete', 'hello');
        $result = $this->repository->delete('an_option_to_delete');
        self::assertTrue($result);

        $this->expectException(OptionNotFoundException::class);
        $this->repository->find('an_option_to_delete');
    }

    public function testDeleteNonExistentOptionReturnsFalse(): void
    {
        self::assertFalse($this->repository->update('another_nonexistent_option', 'hello'));
    }
}
