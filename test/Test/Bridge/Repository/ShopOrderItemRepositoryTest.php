<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\ShopOrderItem;
use Williarin\WordpressInterop\Bridge\Repository\ShopOrderItemRepository;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Test\TestCase;

class ShopOrderItemRepositoryTest extends TestCase
{
    private ShopOrderItemRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(ShopOrderItem::class);
    }

    public function testFindReturnsCorrectShopOrderItem(): void
    {
        $item = $this->repository->find(3);
        self::assertInstanceOf(ShopOrderItem::class, $item);
        self::assertSame(3, $item->orderItemId);
        self::assertSame('Long Sleeve Tee', $item->orderItemName);
        self::assertSame('line_item', $item->orderItemType);
    }

    public function testFindThrowsExceptionIfNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->repository->find(150);
    }

    public function testFindAllReturnsCorrectNumberOfShopOrderItems(): void
    {
        $items = $this->repository->findAll();
        self::assertContainsOnlyInstancesOf(ShopOrderItem::class, $items);
        self::assertCount(5, $items);
    }

    public function testFindOneBy(): void
    {
        $item = $this->repository->findOneByOrderItemName('Cap');
        self::assertSame(4, $item->orderItemId);
    }

    public function testUpdateField(): void
    {
        $this->repository->updateOrderItemName(4, 'Blue horse');
        $item = $this->repository->find(4);
        self::assertSame('Blue horse', $item->orderItemName);
    }
}
