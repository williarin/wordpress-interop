<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Page;
use Williarin\WordpressInterop\Bridge\Repository\PageRepository;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Test\TestCase;

class PageRepositoryTest extends TestCase
{
    private PageRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PageRepository($this->manager, $this->serializer);
    }

    public function testFindThrowsExceptionIfPostTypeDiffers(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->repository->find(1);
    }

    public function testFindReturnsCorrectPage(): void
    {
        $page = $this->repository->find(2);
        self::assertInstanceOf(Page::class, $page);
        self::assertEquals(2, $page->id);
        self::assertEquals('Sample Page', $page->postTitle);
        self::assertStringContainsString('This is an example page.', $page->postContent);
    }

    public function testFindThrowsExceptionIfNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->repository->find(150);
    }

    public function testFindAllReturnsCorrectNumberOfPages(): void
    {
        $pages = $this->repository->findAll();
        self::assertContainsOnlyInstancesOf(Page::class, $pages);
        self::assertCount(7, $pages);
    }
}
