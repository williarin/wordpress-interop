<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\Bridge\Repository\PostRepository;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
use Williarin\WordpressInterop\Exception\InvalidOrderByOrientationException;
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

    public function testFindAllReturnsCorrectNumberOfPosts(): void
    {
        $posts = $this->repository->findAll();
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertCount(4, $posts);
    }

    public function testFindOneByPostTitle(): void
    {
        $post = $this->repository->findOneBy(['post_title' => 'Another post']);
        self::assertInstanceOf(Post::class, $post);
        self::assertEquals(11, $post->id);
        self::assertEquals('Another post', $post->postTitle);
        self::assertEquals('Another small post.', $post->postContent);
    }

    public function testFindOneByPostStatusOrderByAsc(): void
    {
        $post = $this->repository->findOneBy(['post_status' => 'publish'], ['id' => 'ASC']);
        self::assertEquals(1, $post->id);
    }

    public function testFindOneByPostStatusOrderByDesc(): void
    {
        $post = $this->repository->findOneBy(['post_status' => 'publish'], ['id' => 'DESC']);
        self::assertEquals(10, $post->id);
    }

    public function testOrderByFieldValidationWithFindOneBy(): void
    {
        $this->expectException(InvalidFieldNameException::class);
        $this->repository->findOneBy(['post_status' => 'publish'], ['wrong' => 'DESC']);
    }

    public function testOrderByOrientationValidationWithFindOneBy(): void
    {
        $this->expectException(InvalidOrderByOrientationException::class);
        $this->repository->findOneBy(['post_status' => 'publish'], ['id' => 'wrong']);
    }

    public function testFindOneByPostTitleUsingMagicCall(): void
    {
        $post = $this->repository->findOneByPostTitle('Another post');
        self::assertInstanceOf(Post::class, $post);
        self::assertEquals(11, $post->id);
        self::assertEquals('Another post', $post->postTitle);
        self::assertEquals('Another small post.', $post->postContent);
    }

    public function testFindOneByPostStatusOrderByAscUsingMagicCall(): void
    {
        $post = $this->repository->findOneByPostStatus('publish', ['id' => 'ASC']);
        self::assertEquals(1, $post->id);
    }

    public function testFindOneByPostStatusOrderByDescUsingMagicCall(): void
    {
        $post = $this->repository->findOneByPostStatus('publish', ['id' => 'DESC']);
        self::assertEquals(10, $post->id);
    }

    public function testOrderByFieldValidationWithFindOneByUsingMagicCall(): void
    {
        $this->expectException(InvalidFieldNameException::class);
        $this->repository->findOneByPostStatus('publish', ['wrong' => 'DESC']);
    }

    public function testFindLatestPublishedPostWithTheMostComments(): void
    {
        $post = $this->repository->findOneByPostStatus('publish', ['comment_count' => 'DESC', 'post_date' => 'DESC']);
        self::assertEquals(1, $post->id);
    }
}
