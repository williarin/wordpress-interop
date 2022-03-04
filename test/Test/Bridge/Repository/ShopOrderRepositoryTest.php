<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Page;
use Williarin\WordpressInterop\Bridge\Entity\ShopOrder;
use Williarin\WordpressInterop\Bridge\Repository\ShopOrderRepository;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Test\TestCase;

class ShopOrderRepositoryTest extends TestCase
{
    private ShopOrderRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(ShopOrder::class);
    }

    public function testFindThrowsExceptionIfPostTypeDiffers(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->repository->find(1);
    }

    public function testFindReturnsCorrectShopOrder(): void
    {
        $shopOrder = $this->repository->find(63);
        self::assertInstanceOf(ShopOrder::class, $shopOrder);
        self::assertSame(63, $shopOrder->id);
        self::assertSame('Marion', $shopOrder->shippingCity);
        self::assertSame('135 Wyandot Ave', $shopOrder->billingAddress1);
    }

    public function testFindThrowsExceptionIfNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->repository->find(150);
    }

    public function testFindAllReturnsCorrectNumberOfShopOrders(): void
    {
        $shopOrders = $this->repository->findAll();
        self::assertContainsOnlyInstancesOf(ShopOrder::class, $shopOrders);
        self::assertCount(3, $shopOrders);
    }
}
