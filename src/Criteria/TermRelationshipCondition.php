<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Criteria;

final class TermRelationshipCondition
{
    public const IDENTIFIER = 'identifier';

    public function __construct(
        private array $criteria,
        private string $joinConditionField = self::IDENTIFIER,
        private ?string $termTableAlias = null,
    ) {
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }

    public function getJoinConditionField(): string
    {
        return $this->joinConditionField;
    }

    public function getTermTableAlias(): ?string
    {
        return $this->termTableAlias;
    }
}
