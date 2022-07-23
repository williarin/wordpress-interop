<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Attachment;
use Williarin\WordpressInterop\Bridge\Repository\EntityRepositoryInterface;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Criteria\TermRelationshipCondition;
use Williarin\WordpressInterop\Test\TestCase;

use function Williarin\WordpressInterop\Util\String\select_from_eav;

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
            sprintf('%s/featuredimage.png', date('Y/m')),
            '2019/01/beanie-2.jpg',
            '2019/01/hoodie-with-zipper-2.jpg',
        ], array_column($attachments, 'attachedFile'));

        self::assertEquals([4, 18, 23], array_column($attachments, 'originalPostId'));
    }

    public function testGetFeaturedImagesAndAssociatedProductCategory(): void
    {
        $attachments = $this->repository
            ->setOptions([
                'allow_extra_properties' => true,
            ])
            ->findBy([
                new SelectColumns(['id', 'name AS category']),
                new RelationshipCondition(
                    new Operand([18, 23, 26, 27], Operand::OPERATOR_IN),
                    '_thumbnail_id',
                    'original_post_id',
                ),
                new TermRelationshipCondition(
                    [
                        'taxonomy' => 'product_cat',
                    ],
                    '_thumbnail_id',
                ),
            ])
        ;

        self::assertEquals([47, 52, 55, 56], array_column($attachments, 'id'));
        self::assertEquals([18, 23, 26, 27], array_column($attachments, 'originalPostId'));
        self::assertEquals(['Accessories', 'Hoodies', 'Music', 'Music'], array_column($attachments, 'category'));
    }
}
