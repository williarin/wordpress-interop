<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;

final class EntityNotFoundException extends Exception
{
    public function __construct(string $entityClassName, array $criteria)
    {
        $message = sprintf(
            'Could not find entity "%s" with %s.',
            $entityClassName,
            implode(', ', array_map(
                static function (mixed $value, int|string $field): string {
                    $field = $value instanceof RelationshipCondition
                        ? sprintf('relationship ID "%s"', $value->getRelationshipId())
                        : $field;

                    if ($value instanceof Operand) {
                        $value = sprintf('"%s"', $value->getOperand());
                    } elseif ($value instanceof RelationshipCondition) {
                        $value = sprintf('having a field "%s"', $value->getRelationshipFieldName());
                    } else {
                        $value = sprintf('"%s"', $value);
                    }

                    return sprintf('%s %s', $field, $value);
                },
                $criteria,
                array_keys($criteria),
            )),
        );

        parent::__construct($message);
    }
}
