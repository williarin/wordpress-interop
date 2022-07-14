<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Criteria;

use PHPUnit\Framework\TestCase;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;
use Williarin\WordpressInterop\Exception\RelationshipIdDeprecationException;

final class RelationshipCriteriaTest extends TestCase
{
    public function testGetRelationshipIdWithIdGiven(): void
    {
        $condition = new RelationshipCondition(12, '_thumbnail_id');
        self::assertSame(12, $condition->getRelationshipId());
    }

    public function testGetRelationshipIdWithOperandGiven(): void
    {
        $condition = new RelationshipCondition(new Operand('sku', Operand::OPERATOR_EQUAL), '_thumbnail_id');
        $this->expectException(RelationshipIdDeprecationException::class);
        $condition->getRelationshipId();
    }

    public function testGetRelationshipIdOrOperandWithIdGiven(): void
    {
        $condition = new RelationshipCondition(12, '_thumbnail_id');
        self::assertSame(12, $condition->getRelationshipIdOrOperand());
    }

    public function testGetRelationshipIdOrOperandWithOperandGiven(): void
    {
        $condition = new RelationshipCondition(new Operand('sku', Operand::OPERATOR_EQUAL), '_thumbnail_id');
        self::assertInstanceOf(Operand::class, $condition->getRelationshipIdOrOperand());
    }
}
