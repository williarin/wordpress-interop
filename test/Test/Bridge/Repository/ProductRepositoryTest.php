<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\PostMeta;
use Williarin\WordpressInterop\Bridge\Entity\Product;
use Williarin\WordpressInterop\Bridge\Repository\EntityRepositoryInterface;
use Williarin\WordpressInterop\Bridge\Type\GenericData;
use Williarin\WordpressInterop\Criteria\NestedCondition;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Criteria\TermRelationshipCondition;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidTypeException;
use Williarin\WordpressInterop\Test\TestCase;

use function Williarin\WordpressInterop\Util\String\select_from_eav;

class ProductRepositoryTest extends TestCase
{
    /** @phpstan-var \Williarin\WordpressInterop\Bridge\Repository\ProductRepository */
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
        self::assertCount(19, $products);
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

        self::assertSame(64, $product->id);
    }
    public function testFindAllPublishedProducts(): void
    {
        $products = $this->repository->findByPostStatus('publish');
        self::assertIsArray($products);
        self::assertCount(19, $products);
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

    public function testFindByMultipleEAVConditions(): void
    {
        $products = $this->repository->findBy([
            'sku' => new Operand('hoodie.*logo|zipper', Operand::OPERATOR_REGEXP),
            'thumbnail_id' => 52,
        ]);

        self::assertEquals([23], array_column($products, 'id'));
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

    public function testOverrideSelectClause(): void
    {
        $result = $this->repository->createFindByQueryBuilder(
            ['sku' => new Operand('hoodie.*logo|zipper', Operand::OPERATOR_REGEXP)],
            ['sku' => 'ASC']
        )
            ->select('post_title', select_from_eav('sku'))
            ->executeQuery()
            ->fetchAllAssociative();

        self::assertEquals([
            [
                'post_title' => 'Hoodie with Logo',
                'sku' => 'woo-hoodie-with-logo',
            ],
            [
                'post_title' => 'Hoodie with Zipper',
                'sku' => 'woo-hoodie-with-zipper',
            ]
        ], $result);
    }

    public function testOverrideSelectClauseWithinCriteria(): void
    {
        $products = $this->repository->findBy([
            new SelectColumns(['post_title', 'sku']),
            'sku' => new Operand('hoodie.*logo|zipper', Operand::OPERATOR_REGEXP),
        ]);

        $product1 = new Product();
        $product1->postTitle = 'Hoodie with Logo';
        $product1->sku = 'woo-hoodie-with-logo';

        $product2 = new Product();
        $product2->sku = 'woo-hoodie-with-zipper';
        $product2->postTitle = 'Hoodie with Zipper';

        self::assertEquals([$product1, $product2], $products);
    }

    public function testSelectColumnsWithManualEav(): void
    {
        $products = $this->repository->findBy([
            new SelectColumns(['post_title', select_from_eav('sku')]),
            'sku' => new Operand('hoodie.*logo|zipper', Operand::OPERATOR_REGEXP),
        ]);

        $product1 = new Product();
        $product1->postTitle = 'Hoodie with Logo';
        $product1->sku = 'woo-hoodie-with-logo';

        $product2 = new Product();
        $product2->sku = 'woo-hoodie-with-zipper';
        $product2->postTitle = 'Hoodie with Zipper';

        self::assertEquals([$product1, $product2], $products);
    }

    public function testOperatorInWithEavAttribute(): void
    {
        $products = $this->repository->findBySku(
            new Operand(['woo-tshirt', 'woo-single'], Operand::OPERATOR_IN),
        );
        self::assertIsArray($products);
        self::assertCount(2, $products);
        self::assertContainsOnlyInstancesOf(Product::class, $products);
        self::assertEquals([17, 27], array_column($products, 'id'));
    }

    public function testOperatorInWithAssociativeArray(): void
    {
        $products = $this->repository->findBySku(
            new Operand([1 => 'woo-tshirt', 'key' => 'woo-single'], Operand::OPERATOR_IN),
        );
        self::assertIsArray($products);
        self::assertCount(2, $products);
        self::assertContainsOnlyInstancesOf(Product::class, $products);
        self::assertEquals([17, 27], array_column($products, 'id'));
    }

    public function testOperatorInWithEmptyArray(): void
    {
        $products = $this->repository->findBySku(
            new Operand([], Operand::OPERATOR_IN),
        );
        self::assertIsArray($products);
        self::assertCount(0, $products);
    }

    public function testOperandInNestedCondition(): void
    {
        $products = $this->repository->findBy([
            new NestedCondition(NestedCondition::OPERATOR_OR, [
                'sku' => new Operand(['woo-tshirt', 'woo-single'], Operand::OPERATOR_IN),
                'id' => new Operand([19, 20], Operand::OPERATOR_IN),
            ]),
        ]);
        self::assertIsArray($products);
        self::assertCount(4, $products);
        self::assertEquals([17, 19, 20, 27], array_column($products, 'id'));
    }

    public function testNestedConditionAndOperator(): void
    {
        $products = $this->repository->findBy([
            new NestedCondition(NestedCondition::OPERATOR_AND, [
                'sku' => new Operand(['woo-tshirt', 'woo-single'], Operand::OPERATOR_IN),
                'id' => 17,
            ]),
        ]);
        self::assertIsArray($products);
        self::assertCount(1, $products);
        self::assertEquals([17], array_column($products, 'id'));
    }

    public function testTermRelationshipConditionWithTaxonomyOnly(): void
    {
        $products = $this->repository->findBy([
            new TermRelationshipCondition([
                'taxonomy' => 'product_cat',
            ]),
        ]);
        self::assertIsArray($products);
        self::assertCount(19, $products);
        self::assertContainsOnlyInstancesOf(Product::class, $products);
    }

    public function testTermRelationshipConditionWithTaxonomyAndTerm(): void
    {
        $products = $this->repository->findBy([
            new TermRelationshipCondition([
                'taxonomy' => 'product_cat',
                'name' => 'Hoodies',
            ]),
        ]);
        self::assertIsArray($products);
        self::assertCount(5, $products);
        self::assertContainsOnlyInstancesOf(Product::class, $products);
        self::assertEquals([
            'Hoodie',
            'Hoodie with Logo',
            'Hoodie with Pocket',
            'Hoodie with Zipper',
            'Special Forces Hoodie',
        ], array_column($products, 'postTitle'));
    }

    public function testTermRelationshipConditionWithTermOnly(): void
    {
        $products = $this->repository->findBy([
            new TermRelationshipCondition([
                'name' => 'Music',
            ]),
        ]);
        self::assertIsArray($products);
        self::assertCount(2, $products);
        self::assertContainsOnlyInstancesOf(Product::class, $products);
        self::assertEquals([
            'Album',
            'Single',
        ], array_column($products, 'postTitle'));
    }

    public function testTermRelationshipConditionWithInOperator(): void
    {
        $products = $this->repository->findBy([
            new TermRelationshipCondition([
                'name' => new Operand(['featured', 'Accessories'], Operand::OPERATOR_IN),
            ]),
        ]);

        self::assertCount(8, $products);
        self::assertEquals([14, 18, 19, 20, 21, 22, 23, 35], call_user_func(function (array $ids) {
            sort($ids);
            return $ids;
        }, array_column($products, 'id')));
    }

    public function testTermRelationshipConditionWithInAllOperator(): void
    {
        $products = $this->repository->findBy([
            new TermRelationshipCondition([
                'slug' => new Operand(['featured', 'accessories'], Operand::OPERATOR_IN_ALL),
            ]),
        ]);

        self::assertCount(2, $products);
        self::assertEquals([20, 21], array_column($products, 'id'));
    }

    public function testTermRelationshipConditionWithInAllOperatorEmptyArray(): void
    {
        $products = $this->repository->findBy([
            new TermRelationshipCondition([
                'slug' => new Operand([], Operand::OPERATOR_IN_ALL),
            ]),
        ]);

        self::assertCount(0, $products);
    }

    public function testTermRelationshipConditionWithInAllOperatorNotReturningResults(): void
    {
        $products = $this->repository->findBy([
            new TermRelationshipCondition([
                'slug' => new Operand(['featured', 'accessories', 'rated-5'], Operand::OPERATOR_IN_ALL),
            ]),
        ]);

        self::assertCount(0, $products);
    }

    public function testTermRelationshipConditionWithTermAndWrongTaxonomy(): void
    {
        $products = $this->repository->findBy([
            new TermRelationshipCondition([
                'taxonomy' => 'category',
                'name' => 'Music',
            ]),
        ]);
        self::assertIsArray($products);
        self::assertCount(0, $products);
    }

    public function testSelectingTermName(): void
    {
        $product = $this->repository->findOneBy([
            new SelectColumns(['id', 'post_title', 'name AS category']),
            new TermRelationshipCondition([
                'taxonomy' => 'product_cat'
            ]),
        ]);

        $expected = new Product();
        $expected->id = 14;
        $expected->postTitle = 'V-Neck T-Shirt';
        $expected->category = 'Tshirts';

        self::assertEquals($expected, $product);
    }

    public function testEmptyTermRelationshipCondition(): void
    {
        $product = $this->repository->findOneBy([
            new SelectColumns(['id', 'name AS category']),
            new TermRelationshipCondition([]),
        ]);

        $expected = new Product();
        $expected->id = 16;
        $expected->category = 'simple';

        self::assertEquals($expected, $product);
    }

    public function testTermRelationshipConditionAndEavCondition(): void
    {
        $product = $this->repository->findOneBy([
                new SelectColumns(['id', 'post_title', 'post_name', 'post_status', 'sku', 'name AS category']),
                new TermRelationshipCondition([
                    'taxonomy' => 'product_cat',
                ]),
                'sku' => 'woo-hoodie-with-zipper',
            ]);

        $expected = new Product();
        $expected->id = 23;
        $expected->postTitle = 'Hoodie with Zipper';
        $expected->postName = 'hoodie-with-zipper';
        $expected->postStatus = 'publish';
        $expected->sku = 'woo-hoodie-with-zipper';
        $expected->category = 'Hoodies';

        self::assertEquals($expected, $product);
    }

    public function testRetrieveCategoryOfRelatedProduct(): void
    {
        $products = $this->repository->findBy([
            new SelectColumns([
                'id',
                'post_title',
                'main.name AS category',
                'related.name AS related_category',
                select_from_eav(fieldName: 'related_product', metaKey: 'related_product'),
            ]),
            new TermRelationshipCondition(
                ['taxonomy' => 'product_cat'],
                termTableAlias: 'main',
            ),
            new TermRelationshipCondition(
                ['taxonomy' => 'product_cat'],
                joinConditionField: 'related_product',
                termTableAlias: 'related',
            ),
            'id' => new Operand([22, 24, 26], Operand::OPERATOR_IN),
        ]);

        self::assertEquals(['Hoodies', 'Tshirts', 'Music'], array_column($products, 'category'));
        self::assertEquals(['Accessories', 'Music', 'Decor'], array_column($products, 'relatedCategory'));
    }

    public function testEmptyStringForIntEAVRetrieval(): void
    {
        $this->manager->getRepository(PostMeta::class)
            ->update(14, '_thumbnail_id', '')
        ;

        $product = $this->repository->find(14);
        self::assertNull($product->thumbnailId);
    }

    public function testWrongTypeForGenericData(): void
    {
        $this->manager->getRepository(PostMeta::class)
            ->update(14, '_default_attributes', null)
        ;

        $product = $this->repository->find(14);
        $expected = new GenericData();
        $expected->data = [];

        self::assertEquals($expected, $product->defaultAttributes);
    }
}
