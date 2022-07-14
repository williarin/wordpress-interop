<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Manipulation;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Williarin\WordpressInterop\Bridge\Entity\Product;
use Williarin\WordpressInterop\Bridge\Entity\Term;
use Williarin\WordpressInterop\Criteria\PostRelationshipCondition;
use Williarin\WordpressInterop\Exception\MissingEntityTypeException;
use Williarin\WordpressInterop\Persistence\DuplicationService;
use Williarin\WordpressInterop\Test\TestCase;

class DuplicationServiceTest extends TestCase
{
    private DuplicationService $duplicationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->duplicationService = new DuplicationService($this->manager, new AsciiSlugger());
    }

    public function testDuplicateByIdWithoutEntityClassNameThrowsException(): void
    {
        $this->expectException(MissingEntityTypeException::class);
        $this->duplicationService->duplicate(23);
    }

    public function testDuplicateById(): void
    {
        $newEntity = $this->duplicationService->duplicate(23, Product::class);
        $originalTerms = $this->manager->getRepository(Term::class)
            ->findBy([
                new PostRelationshipCondition(Product::class, ['id' => 23]),
            ]);

        $newTerms = $this->manager->getRepository(Term::class)
            ->findBy([
                new PostRelationshipCondition(Product::class, ['id' => $newEntity->id]),
            ]);

        self::assertNotSame(23, $newEntity->id);
        self::assertIsNumeric($newEntity->id);
        self::assertSame($newEntity->sku, 'woo-hoodie-with-zipper-copy');
        self::assertSame(array_column($originalTerms, 'name'), array_column($newTerms, 'name'));
        self::assertEquals($originalTerms, $newTerms);
    }

    public function testDuplicateByEntity(): void
    {
        $product = $this->manager->getRepository(Product::class)
            ->findOneBySku('woo-hoodie-with-zipper');

        $newEntity = $this->duplicationService->duplicate($product);

        $originalTerms = $this->manager->getRepository(Term::class)
            ->findBy([
                new PostRelationshipCondition(Product::class, ['id' => $product->id]),
            ]);

        $newTerms = $this->manager->getRepository(Term::class)
            ->findBy([
                new PostRelationshipCondition(Product::class, ['id' => $newEntity->id]),
            ]);

        self::assertNotSame($product->id, $newEntity->id);
        self::assertIsNumeric($newEntity->id);
        self::assertSame($newEntity->sku, 'woo-hoodie-with-zipper-copy');
        self::assertSame(array_column($originalTerms, 'name'), array_column($newTerms, 'name'));
        self::assertEquals($originalTerms, $newTerms);
    }

    public function testDuplicateWithCustomSuffix(): void
    {
        $newEntity = $this->duplicationService->duplicate(
            23,
            Product::class,
            DuplicationService::POST_STATUS_DRAFT,
            ' Custom Suffix',
        );

        self::assertNotSame(23, $newEntity->id);
        self::assertIsNumeric($newEntity->id);
        self::assertSame($newEntity->postTitle, 'Hoodie with Zipper Custom Suffix');
        self::assertSame($newEntity->sku, 'woo-hoodie-with-zipper-custom-suffix');
    }

    public function testDuplicateWithPublishPostStatus(): void
    {
        $newEntity = $this->duplicationService->duplicate(
            23,
            Product::class,
            DuplicationService::POST_STATUS_PUBLISH,
        );

        self::assertNotSame(23, $newEntity->id);
        self::assertIsNumeric($newEntity->id);
        self::assertSame($newEntity->postStatus, 'publish');
    }
}
