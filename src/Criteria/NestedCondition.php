<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Criteria;

final class NestedCondition
{
    public const OPERATOR_AND = 'and';
    public const OPERATOR_OR = 'or';

    public function __construct(
        private string $operator,
        private array $criteria
    ) {
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }
}
