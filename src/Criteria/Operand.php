<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Criteria;

final class Operand
{
    public const OPERATOR_EQUAL = '=';
    public const OPERATOR_NOT_EQUAL = '!=';
    public const OPERATOR_LESS_THAN = '<';
    public const OPERATOR_LESS_THAN_OR_EQUAL = '<=';
    public const OPERATOR_GREATER_THAN = '>';
    public const OPERATOR_GREATER_THAN_OR_EQUAL = '>=';
    public const OPERATOR_LIKE = 'LIKE';
    public const OPERATOR_RLIKE = 'RLIKE';
    public const OPERATOR_REGEXP = 'REGEXP';

    public function __construct(
        private mixed $operand,
        private string $operator
    ) {
    }

    public function getOperand(): mixed
    {
        return $this->operand;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function isLooseOperator(): bool
    {
        return in_array($this->operator, [self::OPERATOR_LIKE, self::OPERATOR_RLIKE, self::OPERATOR_REGEXP], true);
    }
}
