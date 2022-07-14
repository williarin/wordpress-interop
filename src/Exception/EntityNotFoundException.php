<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\PostRelationshipCondition;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Criteria\TermRelationshipCondition;

final class EntityNotFoundException extends Exception
{
    public function __construct(string $entityClassName, array $criteria)
    {
        $message = sprintf(
            'Could not find entity "%s" with %s.',
            $entityClassName,
            $this->implodeCriteria($criteria),
        );

        parent::__construct($message);
    }

    private function implodeCriteria(array $criteria): string
    {
        return implode(', ', array_filter(array_map(
            function (mixed $value, int|string $field): string {
                if ($value instanceof RelationshipCondition) {
                    if (is_int($value->getRelationshipIdOrOperand())) {
                        $field = sprintf('relationship ID "%s"', $value->getRelationshipIdOrOperand());
                    } else {
                        $field = sprintf(
                            'relationship ID %s',
                            $this->getOperandAsString($value->getRelationshipIdOrOperand()),
                        );
                    }
                }

                if ($value instanceof Operand) {
                    $value = $this->getOperandAsString($value);
                } elseif ($value instanceof RelationshipCondition) {
                    $value = sprintf('having a field "%s"', $value->getRelationshipFieldName());
                } elseif ($value instanceof TermRelationshipCondition) {
                    $value = sprintf('%s', $this->implodeCriteria($value->getCriteria()));
                } elseif ($value instanceof PostRelationshipCondition) {
                    $value = sprintf('%s', $this->implodeCriteria($value->getCriteria()));
                } elseif ($value instanceof SelectColumns) {
                    return '';
                } else {
                    $value = sprintf('"%s"', $value);
                }

                return sprintf('%s%s', is_numeric($field) ? '' : $field . ' ', $value);
            },
            $criteria,
            array_keys($criteria),
        )));
    }

    private function getOperandAsString(Operand $operand): string
    {
        return sprintf(
            '%s',
            is_array($operand->getOperand())
                ? $this->implodeCriteria($operand->getOperand())
                : sprintf('"%s"', $operand->getOperand()),
        );
    }
}
