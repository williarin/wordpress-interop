<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Repository\AttachmentRepository;
use Williarin\WordpressInterop\Test\TestCase;

class AttachmentRepositoryTest extends TestCase
{
    private AttachmentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new AttachmentRepository($this->manager, $this->serializer);
    }

    public function testGetFeaturedImageReturnsAttachment(): void
    {
        $attachment = $this->repository->getFeaturedImage(4);
        self::assertEquals(sprintf('%s/featuredimage.png', date('Y/m')), $attachment->attachedFile);
    }
}
