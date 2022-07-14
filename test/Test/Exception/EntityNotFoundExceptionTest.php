<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Exception;

use PHPUnit\Framework\TestCase;
use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\Bridge\Entity\Product;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\PostRelationshipCondition;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Criteria\TermRelationshipCondition;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;

final class EntityNotFoundExceptionTest extends TestCase
{
    public function testRegularCriteria(): void
    {
        $exception = new EntityNotFoundException(Product::class, [
            'id' => 12,
            'sku' => 'hoodie',
        ]);

        self::assertEquals(
            'Could not find entity "Williarin\WordpressInterop\Bridge\Entity\Product" with id "12", sku "hoodie".',
            $exception->getMessage(),
        );
    }

    public function testRelationshipConditionId(): void
    {
        $exception = new EntityNotFoundException(Product::class, [
            new RelationshipCondition(7, '_thumbnail_id'),
            'id' => 12,
            'sku' => 'hoodie',
        ]);

        self::assertEquals(
            'Could not find entity "Williarin\WordpressInterop\Bridge\Entity\Product" with relationship ID "7" having a field "_thumbnail_id", id "12", sku "hoodie".',
            $exception->getMessage(),
        );
    }

    public function testRelationshipConditionOperand(): void
    {
        $exception = new EntityNotFoundException(Product::class, [
            new RelationshipCondition(new Operand([7, 12, 15], Operand::OPERATOR_IN), '_thumbnail_id'),
            'id' => 12,
            'sku' => 'hoodie',
        ]);

        self::assertEquals(
            'Could not find entity "Williarin\WordpressInterop\Bridge\Entity\Product" with relationship ID "7", "12", "15" having a field "_thumbnail_id", id "12", sku "hoodie".',
            $exception->getMessage(),
        );
    }

    public function testOperand(): void
    {
        $exception = new EntityNotFoundException(Product::class, [
            'post_status' => new Operand(['publish', 'private'], Operand::OPERATOR_IN),
        ]);

        self::assertEquals(
            'Could not find entity "Williarin\WordpressInterop\Bridge\Entity\Product" with post_status "publish", "private".',
            $exception->getMessage(),
        );
    }

    public function testTermRelationshipCondition(): void
    {
        $exception = new EntityNotFoundException(Product::class, [
            new TermRelationshipCondition([
                'taxonomy' => 'product_cat',
                'name' => 'Hoodies',
            ]),
        ]);

        self::assertEquals(
            'Could not find entity "Williarin\WordpressInterop\Bridge\Entity\Product" with taxonomy "product_cat", name "Hoodies".',
            $exception->getMessage(),
        );
    }

    public function testPostRelationshipCondition(): void
    {
        $exception = new EntityNotFoundException(Product::class, [
            new PostRelationshipCondition(Product::class, [
                'id' => 12,
            ]),
        ]);

        self::assertEquals(
            'Could not find entity "Williarin\WordpressInterop\Bridge\Entity\Product" with id "12".',
            $exception->getMessage(),
        );
    }

    public function testSelectColumns(): void
    {
        $exception = new EntityNotFoundException(Product::class, [
            new SelectColumns(['id']),
            'id' => 15,
        ]);

        self::assertEquals(
            'Could not find entity "Williarin\WordpressInterop\Bridge\Entity\Product" with id "15".',
            $exception->getMessage(),
        );
    }

}
