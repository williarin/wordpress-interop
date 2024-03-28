<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Product;
use Williarin\WordpressInterop\Bridge\Entity\Term;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Bridge\Repository\TermRepository;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\PostRelationshipCondition;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Test\TestCase;

use function Williarin\WordpressInterop\Util\String\field_to_property;

class TermRepositoryTest extends TestCase
{
    /** @var TermRepository */
    private RepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(Term::class);
    }

    public function testFind(): void
    {
        $term = $this->repository->find(18);

        self::assertInstanceOf(Term::class, $term);
        self::assertSame('Accessories', $term->name);
    }

    public function testFindOneByAscending(): void
    {
        $term = $this->repository->findOneBy(
            ['taxonomy' => 'product_cat', 'count' => 5],
            ['term_id' => 'ASC'],
        );

        self::assertInstanceOf(Term::class, $term);
        self::assertSame(16, $term->termId);
        self::assertSame('Tshirts', $term->name);
        self::assertSame('tshirts', $term->slug);
        self::assertSame('product_cat', $term->taxonomy);
        self::assertSame(16, $term->termTaxonomyId);
        self::assertSame(5, $term->count);
    }

    public function testFindOneByDescending(): void
    {
        $term = $this->repository->findOneBy(
            ['taxonomy' => 'product_cat', 'count' => 5],
            ['term_id' => 'DESC'],
        );

        self::assertInstanceOf(Term::class, $term);
        self::assertSame('Accessories', $term->name);
    }

    public function testFindByTaxonomy(): void
    {
        $terms = $this->repository->findByTaxonomy('product_cat');

        self::assertContainsOnlyInstancesOf(Term::class, $terms);
        self::assertSame(range(15, 21), array_column($terms, 'termId'));
    }

    public function testFindOneBySelectColumn(): void
    {
        $term = $this->repository->findOneBy([
            new SelectColumns(['term_id', 'name']),
            'term_id' => 16,
        ]);

        $expected = new Term();
        $expected->termId = 16;
        $expected->name = 'Tshirts';

        self::assertEquals($expected, $term);
    }

    public function testFindByPostRelationshipCondition(): void
    {
        $terms = $this->repository->findBy([
            new SelectColumns(['taxonomy', 'name']),
            new PostRelationshipCondition(Product::class, [
                'post_status' => new Operand(['publish', 'private'], Operand::OPERATOR_IN),
                'sku' => 'super-forces-hoodie',
            ]),
            'taxonomy' => new Operand(['product_tag', 'product_type', 'product_visibility'], Operand::OPERATOR_NOT_IN),
        ]);

        self::assertEquals([
            'product_cat' => 'Hoodies',
            'pa_manufacturer' => 'MegaBrand',
        ], array_combine(array_column($terms, 'taxonomy'), array_column($terms, 'name')));
    }

    public function testCreateNewTermForTaxonomy(): void
    {
        $term = $this->repository->createTermForTaxonomy('Jewelry', 'product_cat');

        $this->validateTerm($term, [
            'name' => 'Jewelry',
            'slug' => 'jewelry',
            'taxonomy' => 'product_cat',
            'count' => 0,
        ]);
    }

    public function testCreateTermForTaxonomyNoDuplicate(): void
    {
        $term1 = $this->repository->createTermForTaxonomy('Jewelry', 'product_cat');
        $term2 = $this->repository->createTermForTaxonomy('Jewelry', 'product_cat');

        self::assertEquals($term1, $term2);
        self::assertNotSame($term1, $term2);
    }

    public function testAddTermsToEntity(): void
    {
        $hoodieTerms = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'post_status' => new Operand(['publish', 'private'], Operand::OPERATOR_IN),
                'sku' => 'super-forces-hoodie',
            ]),
            'taxonomy' => new Operand(['product_tag', 'product_type', 'product_visibility'], Operand::OPERATOR_NOT_IN),
        ]);

        $product = $this->manager->getRepository(Product::class)
            ->find(37)
        ;

        $termsProduct = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
            ]),
        ]);

        self::assertEquals(['external', 'Decor'], array_column($termsProduct, 'name'));

        $this->repository->addTermsToEntity($product, $hoodieTerms);

        $termsProduct = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
            ]),
        ]);

        self::assertEquals(['external', 'Hoodies', 'Decor', 'MegaBrand'], array_column($termsProduct, 'name'));
        self::assertEquals([1, 6, 1, 2], array_column($termsProduct, 'count'));
    }

    public function testAddDuplicateTermsToEntityDoesNotDuplicateThem(): void
    {
        $hoodieTerms = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'post_status' => new Operand(['publish', 'private'], Operand::OPERATOR_IN),
                'sku' => 'super-forces-hoodie',
            ]),
        ]);

        $product = $this->manager->getRepository(Product::class)
            ->find(16)
        ;

        $termsProduct = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
            ]),
        ]);

        self::assertEquals(['simple', 'Hoodies'], array_column($termsProduct, 'name'));

        $this->repository->addTermsToEntity($product, $hoodieTerms);

        $termsProduct = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
            ]),
        ]);

        self::assertEquals(['simple', 'Hoodies', 'MegaBrand'], array_column($termsProduct, 'name'));
        self::assertEquals([15, 5, 2], array_column($termsProduct, 'count'));
    }

    public function testAddTermsToEntityWithoutTermTaxonomyIdAreIgnored(): void
    {
        $hoodieTerms = array_map(
            static function (Term $term) {
                $term->termTaxonomyId = null;
                return $term;
            },
            $this->repository->findBy([
                new PostRelationshipCondition(Product::class, [
                    'post_status' => new Operand(['publish', 'private'], Operand::OPERATOR_IN),
                    'sku' => 'super-forces-hoodie',
                ]),
                'taxonomy' => new Operand(['product_tag', 'product_type', 'product_visibility'], Operand::OPERATOR_NOT_IN),
            ]),
        );

        $product = $this->manager->getRepository(Product::class)
            ->find(37)
        ;

        $termsProduct = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
            ]),
        ]);

        self::assertEquals(['external', 'Decor'], array_column($termsProduct, 'name'));

        $this->repository->addTermsToEntity($product, $hoodieTerms);

        $termsProduct = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
            ]),
        ]);

        self::assertEquals(['external', 'Decor'], array_column($termsProduct, 'name'));
    }

    public function testRemoveTermsFromEntity(): void
    {
        $product = $this->manager->getRepository(Product::class)
            ->findOneBySku('super-forces-hoodie')
        ;

        $terms = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
                'taxonomy' => new Operand(['product_cat', 'pa_manufacturer'], Operand::OPERATOR_IN),
            ]),
        ]);

        self::assertEquals(['Hoodies', 'MegaBrand'], array_column($terms, 'name'));

        $this->repository->removeTermsFromEntity($product, $terms);

        $terms = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
            ]),
        ]);

        self::assertEquals(['simple'], array_column($terms, 'name'));
    }

    public function testRemoveTermsFromEntityWhichDoesNotHaveThemDoesNothing(): void
    {
        $product = $this->manager->getRepository(Product::class)
            ->findOneBySku('super-forces-hoodie')
        ;

        $terms = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
            ]),
        ]);

        self::assertEquals(['simple', 'Hoodies', 'MegaBrand'], array_column($terms, 'name'));

        $unrelatedTerms = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => 37,
            ]),
        ]);

        self::assertEquals(['external', 'Decor'], array_column($unrelatedTerms, 'name'));

        $this->repository->removeTermsFromEntity($product, $unrelatedTerms);

        $terms = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
            ]),
        ]);

        self::assertEquals(['simple', 'Hoodies', 'MegaBrand'], array_column($terms, 'name'));
    }

    public function testRemoveTermsFromEntityWithoutTermTaxonomyIdAreIgnored(): void
    {
        $product = $this->manager->getRepository(Product::class)
            ->findOneBySku('super-forces-hoodie')
        ;

        $terms = array_map(
            static function (Term $term) {
                $term->termTaxonomyId = null;
                return $term;
            },
            $this->repository->findBy([
                new PostRelationshipCondition(Product::class, [
                    'id' => $product->id,
                    'taxonomy' => new Operand(['product_cat', 'pa_manufacturer'], Operand::OPERATOR_IN),
                ]),
            ]),
        );

        self::assertEquals(['Hoodies', 'MegaBrand'], array_column($terms, 'name'));

        $this->repository->removeTermsFromEntity($product, $terms);

        $terms = $this->repository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $product->id,
            ]),
        ]);

        self::assertEquals(['simple', 'Hoodies', 'MegaBrand'], array_column($terms, 'name'));
    }

    private function validateTerm(Term $term, array $values): void
    {
        foreach ($values as $key => $value) {
            self::assertEquals($value, $term->{field_to_property($key)});
        }

        self::assertIsInt($term->termId);
        self::assertIsInt($term->termTaxonomyId);
    }
}
