<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\Bridge\Repository\EntityRepositoryInterface;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
use Williarin\WordpressInterop\Exception\InvalidTypeException;
use Williarin\WordpressInterop\Test\TestCase;

class AbstractEntityRepositoryTest extends TestCase
{
    private EntityRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(Post::class);
    }

    public function testUpdateSingleField(): void
    {
        $this->repository->updateSingleField(10, 'post_content', 'Just a small post.');
        self::assertSame('Just a small post.', $this->repository->find(10)->postContent);

        $this->repository->updateSingleField(10, 'post_content', 'A new content');
        self::assertSame('A new content', $this->repository->find(10)->postContent);

        $this->repository->updateSingleField(10, 'post_content', 'Just a small post.');
    }

    public function testUpdateNonExistentFieldThrowsException(): void
    {
        $this->expectException(InvalidFieldNameException::class);
        $this->repository->updateSingleField(5000, 'nonexistent_field', 'Never mind');
    }

    public function testUpdateSingleFieldPostContentMatchesExpectedStringType(): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->repository->updateSingleField(10, 'post_content', 50);
    }

    public function testUpdateSingleFieldPostAuthorMatchesExpectedIntType(): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->repository->updateSingleField(10, 'post_author', 'This should be an int');
    }

    public function testUpdatePostContentWithMagicCall(): void
    {
        $this->repository->updatePostContent(10, 'Just a small post.');
        self::assertSame('Just a small post.', $this->repository->find(10)->postContent);

        $this->repository->updatePostContent(10, 'A new content');
        self::assertSame('A new content', $this->repository->find(10)->postContent);

        $this->repository->updatePostContent(10, 'Just a small post.');
    }

    public function testUpdateFieldWithMagicCallWithoutArgumentThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Some arguments are missing.');
        $this->repository->updatePostContent();
    }

    public function testUpdateFieldWithMagicCallWithOneArgumentThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Some arguments are missing.');
        $this->repository->updatePostContent(120);
    }

    public function testUpdateFieldWithMagicCallWithWrongArgumentTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Arguments provided are of the wrong type.');
        $this->repository->updatePostContent("wrong", "new value");
    }
}
