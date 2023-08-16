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
    public const OPERATOR_IN = 'IN';
    public const OPERATOR_NOT_IN = 'NOT IN';
    public const OPERATOR_IN_ALL = 'IN_ALL';
    public const OPERATOR_IS_NULL = 'IS NULL';
    public const OPERATOR_IS_NOT_NULL = 'IS NOT NULL';

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
        return in_array($this->operator, [
            self::OPERATOR_LIKE,
            self::OPERATOR_RLIKE,
            self::OPERATOR_REGEXP,
            self::OPERATOR_IN,
            self::OPERATOR_IN_ALL,
            self::OPERATOR_NOT_IN,
        ], true);
    }

    public function isStandaloneOperator(): bool
    {
        return in_array($this->operator, [self::OPERATOR_IS_NULL, self::OPERATOR_IS_NOT_NULL]);
    }
}
