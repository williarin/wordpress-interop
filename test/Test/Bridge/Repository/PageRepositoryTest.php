<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Page;
use Williarin\WordpressInterop\Bridge\Repository\EntityRepositoryInterface;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
use Williarin\WordpressInterop\Test\TestCase;

use function Williarin\WordpressInterop\Util\String\select_from_eav;

class PageRepositoryTest extends TestCase
{
    /** @phpstan-var \Williarin\WordpressInterop\Bridge\Repository\PageRepository */
    private EntityRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(Page::class);
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

    public function testFindOneByUndefinedEavDefault(): void
    {
        $this->expectException(InvalidFieldNameException::class);
        $this->repository->findBy([
            new SelectColumns(['id', select_from_eav('wp_page_template')]),
            'post_status' => 'publish',
            'wp_page_template' => 'default',
        ]);
    }

    public function testFindOneByUndefinedEavAllowExtraProperties(): void
    {
        $pages = $this->repository
            ->setOptions([
                'allow_extra_properties' => true,
            ])
            ->findBy([
                new SelectColumns(['id', select_from_eav('wp_page_template')]),
                'post_status' => 'publish',
                'wp_page_template' => 'default',
            ])
        ;

        self::assertContainsOnlyInstancesOf(Page::class, $pages);
        self::assertCount(1, $pages);
        self::assertEquals(2, $pages[0]->id);
        self::assertEquals('default', $pages[0]->wpPageTemplate);
    }
}
