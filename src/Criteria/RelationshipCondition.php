<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Criteria;

use JetBrains\PhpStorm\Deprecated;
use Williarin\WordpressInterop\Exception\RelationshipIdDeprecationException;

final class RelationshipCondition
{
    public function __construct(
        private int|Operand $relationshipIdOrOperand,
        private string $relationshipFieldName,
        private ?string $alias = null,
    ) {
    }

    #[Deprecated(
        reason: 'Since 1.8.0, use getRelationshipIdOrOperand() instead',
        replacement: '%class%->getRelationshipIdOrOperand()'
    )]
    public function getRelationshipId(): int
    {
        if (is_int($this->relationshipIdOrOperand)) {
            return $this->relationshipIdOrOperand;
        }

        throw new RelationshipIdDeprecationException();
    }

    public function getRelationshipIdOrOperand(): int|Operand
    {
        return $this->relationshipIdOrOperand;
    }

    public function getRelationshipFieldName(): string
    {
        return $this->relationshipFieldName;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
    }
}
