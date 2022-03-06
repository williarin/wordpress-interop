<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;
use Williarin\WordpressInterop\Criteria\Operand;
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
                $field = $value instanceof RelationshipCondition
                    ? sprintf('relationship ID "%s"', $value->getRelationshipId())
                    : $field;

                if ($value instanceof Operand) {
                    $value = sprintf('"%s"', $value->getOperand());
                } elseif ($value instanceof RelationshipCondition) {
                    $value = sprintf('having a field "%s"', $value->getRelationshipFieldName());
                } elseif ($value instanceof TermRelationshipCondition) {
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
}
