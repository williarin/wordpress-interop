<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Attachment;
use Williarin\WordpressInterop\Bridge\Repository\EntityRepositoryInterface;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;
use Williarin\WordpressInterop\Test\TestCase;

class AttachmentRepositoryTest extends TestCase
{
    private EntityRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(Attachment::class);
    }

    public function testFindAttachmentPopulatesAttributes(): void
    {
        $attachment = $this->repository->find(13);
        self::assertEquals(sprintf('%s/featuredimage.png', date('Y/m')), $attachment->attachedFile);
    }

    public function testGetFeaturedImageReturnsAttachment(): void
    {
        $attachment = $this->repository->getFeaturedImage(4);
        self::assertEquals(sprintf('%s/featuredimage.png', date('Y/m')), $attachment->attachedFile);
    }

    public function testGetFeaturedImageUsingRelationshipCondition(): void
    {
        $attachment = $this->repository->findOneBy([
            new RelationshipCondition(4, '_thumbnail_id'),
        ]);
        self::assertEquals(sprintf('%s/featuredimage.png', date('Y/m')), $attachment->attachedFile);
    }

    public function testGetFeaturedImageUsingRelationshipConditionWithPostId(): void
    {
        $attachment = $this->repository->findOneBy([
            new RelationshipCondition(4, '_thumbnail_id', 'original_post_id'),
        ]);
        self::assertEquals(4, $attachment->originalPostId);
    }

    public function testGetFeaturedImagesOfMultiplePosts(): void
    {
        $attachments = $this->repository->findBy([
            new RelationshipCondition(
                new Operand([4, 13, 18, 23], Operand::OPERATOR_IN),
                '_thumbnail_id',
                'original_post_id',
            ),
        ]);
        self::assertEquals([
            '2022/06/featuredimage.png',
            '2019/01/beanie-2.jpg',
            '2019/01/hoodie-with-zipper-2.jpg',
        ], array_column($attachments, 'attachedFile'));

        self::assertEquals([4, 18, 23], array_column($attachments, 'originalPostId'));
    }
}
