<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Page;
use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\Bridge\Repository\EntityRepositoryInterface;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Exception\InvalidEntityException;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
use Williarin\WordpressInterop\Exception\InvalidOrderByOrientationException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
use Williarin\WordpressInterop\Test\TestCase;

class PostRepositoryTest extends TestCase
{
    /** @phpstan-var \Williarin\WordpressInterop\Bridge\Repository\PostRepository */
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

    public function testMagicCallNonExistentMethod(): void
    {
        $this->expectException(MethodNotFoundException::class);
        $this->repository->nothingLikeThis();
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

    public function testOperatorNotIn(): void
    {
        $posts = $this->repository->findBy(['post_status' => new Operand(['draft', 'publish'], Operand::OPERATOR_NOT_IN)]);
        self::assertIsArray($posts);
        self::assertCount(1, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertEquals([11], array_column($posts, 'id'));
    }

    public function testOperatorNotInEmptyArray(): void
    {
        $posts = $this->repository->findBy(['post_status' => new Operand([], Operand::OPERATOR_NOT_IN)]);
        self::assertIsArray($posts);
        self::assertCount(4, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertEquals([12, 11, 1, 10], array_column($posts, 'id'));
    }

    public function testOperatorNotInWithMagicMethod(): void
    {
        $posts = $this->repository->findByPostStatus(new Operand(['draft', 'private'], Operand::OPERATOR_NOT_IN));
        self::assertIsArray($posts);
        self::assertCount(2, $posts);
        self::assertContainsOnlyInstancesOf(Post::class, $posts);
        self::assertEquals([1, 10], array_column($posts, 'id'));
    }

    public function testPersistWrongEntityThrowsException(): void
    {
        $page = new Page();

        $this->expectException(InvalidEntityException::class);
        $this->repository->persist($page);
    }

    public function testPersistNewPost(): void
    {
        $post = new Post();
        $post->postAuthor = 1;
        $post->postTitle = 'Newly created post';
        $post->postName = 'newly-created-post';
        $post->postContent = 'How are you?';
        $post->postContentFiltered = '';
        $post->postExcerpt = '';
        $post->postStatus = 'draft';
        $post->commentStatus = 'open';
        $post->commentCount = 0;
        $post->pingStatus = 'open';
        $post->postPassword = '';
        $post->toPing = '';
        $post->pinged = '';
        $post->postModified = new \DateTime();
        $post->postModifiedGmt = new \DateTime();
        $post->postDate = new \DateTime();
        $post->postDateGmt = new \DateTime();
        $post->postParent = 0;
        $post->guid = "http://localhost/$post->postName";
        $post->menuOrder = 0;
        $post->postType = 'post';
        $post->postMimeType = '';

        $this->repository->persist($post);
        self::assertIsNumeric($post->id);
    }

    public function testPersistExistingPost(): void
    {
        $post = $this->repository->findOneByPostTitle('Another post');
        self::assertSame(11, $post->id);

        $post->postTitle = 'Another post with a new title';
        $post->postStatus = 'publish';

        $this->repository->persist($post);

        try {
            $this->repository->findOneByPostTitle('Another post');
            self::fail();
        } catch (\Exception $e) {
            self::assertSame(EntityNotFoundException::class, $e::class);
        }

        $post = $this->repository->find(11);

        self::assertSame('Another post with a new title', $post->postTitle);
        self::assertSame('publish', $post->postStatus);
    }

    public function testDynamicSetter(): void
    {
        $post = new Post();

        $post = $post->setDynamicProperty('Hello');
        self::assertSame('Hello', $post->dynamicProperty);
    }

    public function testDynamicSetterNoArgument(): void
    {
        $post = new Post();

        $this->expectException(InvalidArgumentException::class);
        $post->setDynamicProperty();
    }

    public function testWrongMethodCall(): void
    {
        $post = new Post();

        $this->expectException(MethodNotFoundException::class);
        $post->nonExistentMethod();
    }

    public function testDynamicGetter(): void
    {
        $post = new Post();
        $post->postContent = 'Hello';

        self::assertSame('Hello', $post->getPostContent());
    }
}
