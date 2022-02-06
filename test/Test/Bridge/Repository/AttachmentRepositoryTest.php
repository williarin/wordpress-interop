<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Attachment;
use Williarin\WordpressInterop\Bridge\Repository\EntityRepositoryInterface;
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
}
