<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\Bridge\Repository\PostRepository;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Test\TestCase;

class PostRepositoryTest extends TestCase
{
    private PostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PostRepository($this->manager, $this->serializer);
    }

    public function testFindReturnsCorrectPost(): void
    {
        $post = $this->repository->find(1);
        self::assertInstanceOf(Post::class, $post);
        self::assertEquals(1, $post->id);
        self::assertEquals('Hello world!', $post->postTitle);
        self::assertStringContainsString('Welcome to WordPress.', $post->postContent);
    }

    public function testFindThrowsExceptionIfNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->repository->find(150);
    }

    public function testFindAllReturns(): void
    {
        $posts = $this->repository->findAll();
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertCount(4, $posts);
    }
}
