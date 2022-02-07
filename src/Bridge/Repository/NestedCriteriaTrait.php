<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Williarin\WordpressInterop\Criteria\NestedCondition;
use Williarin\WordpressInterop\Criteria\Operand;

trait NestedCriteriaTrait
{
    private function createNestedCriteria(
        QueryBuilder $queryBuilder,
        array $criteria,
        NestedCondition $condition
    ): void {
        $normalizedCriteria = $condition->getCriteria();
        $expressions = [];

        foreach ($normalizedCriteria as $field => $value) {
            $expressions[] = sprintf(
                '`%s` %s :%s',
                $field,
                $criteria[$field] instanceof Operand ? $criteria[$field]->getOperator() : '=',
                $field,
            );

            $queryBuilder->setParameter(
                $field,
                $criteria[$field] instanceof Operand ? $criteria[$field]->getOperand() : $value
            );
        }

        $queryBuilder->andWhere($queryBuilder->expr()->{$condition->getOperator()}(...$expressions));
    }
}
