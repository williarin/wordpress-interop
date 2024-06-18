<?php

declare(strict_types=1);

namespace Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Comment;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Bridge\Repository\CommentRepository;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Test\TestCase;

use function Williarin\WordpressInterop\Util\String\select_from_eav;

class CommentRepositoryTest extends TestCase
{
    /** @var CommentRepository */
    private RepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(Comment::class);
    }

    public function testFind(): void
    {
        $term = $this->repository->find(1);

        self::assertInstanceOf(Comment::class, $term);
    }

    public function testFindByAttribute(): void
    {
        $comments = $this->repository
            ->setOptions([
                'allow_extra_properties' => true,
            ])
            ->findBy([
                new SelectColumns(['comment_id', 'comment_post_id', select_from_eav('rating', 'rating')]),
                'rating' => '5',
            ]);

        self::assertContainsOnlyInstancesOf(Comment::class, $comments);
        self::assertCount(1, $comments);
        self::assertSame(2, $comments[0]->commentId);
        self::assertSame('5', $comments[0]->rating);
        self::assertSame(15, $comments[0]->commentPostId);
    }

    public function testFindOneByWithSelectedAttribute(): void
    {
        $comments = $this->repository
            ->setOptions([
                'allow_extra_properties' => true,
            ])
            ->findBy([
                new SelectColumns(['comment_id', 'comment_post_id', select_from_eav('rating', 'rating')]),
            ]);

        self::assertContainsOnlyInstancesOf(Comment::class, $comments);
        self::assertCount(3, $comments);
        self::assertNull($comments[0]->rating);
        self::assertSame('5', $comments[1]->rating);
        self::assertSame('4', $comments[2]->rating);
    }
}
