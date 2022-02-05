<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Product;
use Williarin\WordpressInterop\Bridge\Repository\EntityRepositoryInterface;
use Williarin\WordpressInterop\Criteria\NestedCondition;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidTypeException;
use Williarin\WordpressInterop\Test\TestCase;

class ProductRepositoryTest extends TestCase
{
    private EntityRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(Product::class);
    }

    public function testFindReturnsCorrectProduct(): void
    {
        $product = $this->repository->find(14);
        self::assertInstanceOf(Product::class, $product);
        self::assertSame(14, $product->id);
        self::assertSame('V-Neck T-Shirt', $product->postTitle);
        self::assertSame('woo-vneck-tee', $product->sku);
        self::assertStringContainsString('Pellentesque habitant morbi tristique', $product->postContent);
    }

    public function testFindThrowsExceptionIfNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->repository->find(150);
    }

    public function testFindAllReturnsCorrectNumberOfPosts(): void
    {
        $products = $this->repository->findAll();
        self::assertContainsOnlyInstancesOf(Product::class, $products);
        self::assertCount(18, $products);
    }

    public function testFindBySku(): void
    {
        $product = $this->repository->findOneBySku('woo-vneck-tee');
        self::assertSame(14, $product->id);
        self::assertSame('woo-vneck-tee', $product->sku);
    }

    public function testProductAttributesAreAccessibleAsGenericData(): void
    {
        $product = $this->repository->find(14);
        self::assertSame([
            'name' => 'pa_color',
            'value' => '',
            'position' => 0,
            'is_visible' => 1,
            'is_variation' => 1,
            'is_taxonomy' => 1,
        ], $product->productAttributes->getPaColor());
    }

    public function testProductWeightIsConvertedToFloat(): void
    {
        $product = $this->repository->find(14);
        self::assertSame(0.5, $product->weight);
    }

    public function testLatestPublishedProductInStock(): void
    {
        $product = $this->repository->findOneBy(
            ['stock_status' => 'instock', 'post_status' => 'publish'],
            ['post_date' => 'DESC'],
        );

        self::assertSame(37, $product->id);
    }
    public function testFindAllPublishedProducts(): void
    {
        $products = $this->repository->findByPostStatus('publish');
        self::assertIsArray($products);
        self::assertCount(18, $products);
        self::assertContainsOnlyInstancesOf(Product::class, $products);
    }

    public function testFindProductsWithAHeightOf2(): void
    {
        $products = $this->repository->findByHeight(2.0);
        self::assertIsArray($products);
        self::assertCount(3, $products);
        self::assertContainsOnlyInstancesOf(Product::class, $products);
        self::assertEquals([14, 22, 23], array_column($products, 'id'));
    }

    public function testFindByRegexp(): void
    {
        $products = $this->repository->findBy([
            'post_title' => new Operand('Hoodie.*Pocket|Zipper', Operand::OPERATOR_REGEXP)
        ]);

        self::assertEquals([22, 23], array_column($products, 'id'));
    }

    public function testFindByRegexpWithMagicMethod(): void
    {
        $products = $this->repository->findByPostTitle(
            new Operand('Hoodie.*Pocket|Zipper', Operand::OPERATOR_REGEXP),
        );

        self::assertEquals([22, 23], array_column($products, 'id'));
    }

    public function testFindOneByRegexpWithMagicMethod(): void
    {
        $product = $this->repository->findOneByPostTitle(
            new Operand('Hoodie.*Pocket', Operand::OPERATOR_REGEXP),
        );

        self::assertEquals(22, $product->id);
    }

    public function testFindOneByEAVRegexp(): void
    {
        $product = $this->repository->findOneBySku(
            new Operand('hoodie.*logo', Operand::OPERATOR_REGEXP),
        );

        self::assertEquals(16, $product->id);
    }

    public function testFindByEAVRegexp(): void
    {
        $products = $this->repository->findBySku(
            new Operand('hoodie.*logo|zipper', Operand::OPERATOR_REGEXP),
        );

        self::assertEquals([16, 23], array_column($products, 'id'));
    }

    public function testFindByCriteriaOr(): void
    {
        $products = $this->repository->findBy([
            new NestedCondition(NestedCondition::OPERATOR_OR, [
                'post_title' => new Operand('Hoodie%', Operand::OPERATOR_LIKE),
                'ID' => new Operand('[126]{2}', Operand::OPERATOR_REGEXP),
            ]),
            'stock_status' => 'instock',
        ]);

        self::assertEquals([15, 16, 21, 22, 23, 26], array_column($products, 'id'));
    }

    public function testFindOneByWithLooseOperatorDoesNotThrowException(): void
    {
        $product = $this->repository->findOneByPostAuthor(
            new Operand('2', Operand::OPERATOR_LIKE),
        );

        self::assertEquals(14, $product->id);
    }

    public function testFindOneByWithStrictOperatorThrowsException(): void
    {
        $this->expectException(InvalidTypeException::class);
        $this->repository->findOneByPostAuthor(
            new Operand('2', Operand::OPERATOR_EQUAL),
        );
    }
}
