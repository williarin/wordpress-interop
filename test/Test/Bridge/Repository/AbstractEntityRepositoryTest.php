<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\Bridge\Repository\AbstractEntityRepository;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
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
}
