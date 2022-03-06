<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\Bridge\Repository\EntityRepositoryInterface;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
use Williarin\WordpressInterop\Exception\InvalidOrderByOrientationException;
use Williarin\WordpressInterop\Test\TestCase;

class PostRepositoryTest extends TestCase
{
    private EntityRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(Post::class);
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
        self::assertSame([12, 11, 1, 10], array_column($posts, 'id'));
    }

    public function testFindAllOrderBy(): void
    {
        $posts = $this->repository->findAll(['post_date' => 'DESC']);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertSame([12, 11, 10, 1], array_column($posts, 'id'));
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

    public function testFindAllPostsAuthoredByAdminUser(): void
    {
        $posts = $this->repository->findBy(['post_author' => 1], ['id' => 'DESC']);
        self::assertIsArray($posts);
        self::assertCount(4, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertEquals([12, 11, 10, 1], array_column($posts, 'id'));
    }

    public function testFindTheTwoLatestPosts(): void
    {
        $posts = $this->repository->findBy(['post_author' => 1], ['post_date' => 'DESC'], 2);
        self::assertIsArray($posts);
        self::assertCount(2, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertEquals([12, 11], array_column($posts, 'id'));
    }

    public function testFindTheTwoLatestPostsStartingFromTheThird(): void
    {
        $posts = $this->repository->findBy(['post_author' => 1], ['post_date' => 'DESC'], 2, 2);
        self::assertIsArray($posts);
        self::assertCount(2, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertEquals([10, 1], array_column($posts, 'id'));
    }

    public function testFindLatestPostsUsingMagicMethod(): void
    {
        $posts = $this->repository->findByPostAuthor(1, ['post_date' => 'DESC']);
        self::assertIsArray($posts);
        self::assertCount(4, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertEquals([12, 11, 10, 1], array_column($posts, 'id'));
    }

    public function testOperatorIn(): void
    {
        $posts = $this->repository->findBy(['post_status' => new Operand(['draft', 'publish'], Operand::OPERATOR_IN)]);
        self::assertIsArray($posts);
        self::assertCount(3, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertEquals([12, 1, 10], array_column($posts, 'id'));
    }

    public function testOperatorInWithMagicMethod(): void
    {
        $posts = $this->repository->findByPostStatus(new Operand(['draft', 'private'], Operand::OPERATOR_IN));
        self::assertIsArray($posts);
        self::assertCount(2, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertEquals([12, 11], array_column($posts, 'id'));
    }
}
