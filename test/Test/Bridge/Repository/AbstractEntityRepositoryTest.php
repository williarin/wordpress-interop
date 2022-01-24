<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\Bridge\Repository\AbstractEntityRepository;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
use Williarin\WordpressInterop\Exception\InvalidTypeException;
use Williarin\WordpressInterop\Test\TestCase;

class AbstractEntityRepositoryTest extends TestCase
{
    private AbstractEntityRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new class ($this->manager, $this->serializer, Post::class) extends AbstractEntityRepository {};
    }

    public function testUpdateSingleField(): void
    {
        $this->repository->updateSingleField(4, 'post_content', 'Just a small post.');
        self::assertSame('Just a small post.', $this->repository->find(4)->postContent);

        $this->repository->updateSingleField(4, 'post_content', 'A new content');
        self::assertSame('A new content', $this->repository->find(4)->postContent);

        $this->repository->updateSingleField(4, 'post_content', 'Just a small post.');
    }

    public function testUpdateNonExistentFieldThrowsException(): void
    {
        $this->expectException(InvalidFieldNameException::class);
        $this->repository->updateSingleField(5000, 'nonexistent_field', 'Never mind');
    }

    public function testUpdateSingleFieldPostContentMatchesExpectedStringType(): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->repository->updateSingleField(4, 'post_content', 50);
    }

    public function testUpdateSingleFieldPostAuthorMatchesExpectedIntType(): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->repository->updateSingleField(4, 'post_author', 'This should be an int');
    }

    public function testUpdatePostContentWithMagicCall(): void
    {
        $this->repository->updatePostContent(4, 'Just a small post.');
        self::assertSame('Just a small post.', $this->repository->find(4)->postContent);

        $this->repository->updatePostContent(4, 'A new content');
        self::assertSame('A new content', $this->repository->find(4)->postContent);

        $this->repository->updatePostContent(4, 'Just a small post.');
    }

    public function testUpdateFieldWithMagicCallWithoutArgumentThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This method requires two arguments: updatePostContent(int $id, mixed $newValue).');
        $this->repository->updatePostContent();
    }

    public function testUpdateFieldWithMagicCallWithOneArgumentThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('This method requires two arguments: updatePostContent(int $id, mixed $newValue).');
        $this->repository->updatePostContent(120);
    }

    public function testUpdateFieldWithMagicCallWithWrongArgumentTypeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The option "$id" with value "wrong" is expected to be of type "int", but is of type "string".');
        $this->repository->updatePostContent("wrong", "new value");
    }
}
