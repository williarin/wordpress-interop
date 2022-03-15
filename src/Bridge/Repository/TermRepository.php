<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Williarin\WordpressInterop\Bridge\Entity\Term;
use Williarin\WordpressInterop\Criteria\SelectColumns;

class TermRepository extends AbstractEntityRepository
{
    protected const TABLE_NAME = 'terms';
    protected const TABLE_META_NAME = 'termmeta';
    protected const TABLE_IDENTIFIER = 'term_id';
    protected const TABLE_META_IDENTIFIER = 'term_id';
    protected const FALLBACK_ENTITY = Term::class;

    public function __construct()
    {
        parent::__construct(Term::class);
    }

    public function createFindByQueryBuilder(array $criteria, ?array $orderBy): QueryBuilder
    {
        $queryBuilder = parent::createFindByQueryBuilder($criteria, $orderBy)
            ->innerJoin(
                'p',
                $this->entityManager->getTablesPrefix() . 'term_taxonomy',
                'tt',
                'p.term_id = tt.term_id',
            )
            ->addGroupBy('tt.taxonomy')
        ;

        if (\count(array_filter($criteria, static fn ($condition) => $condition instanceof SelectColumns)) === 0) {
            $queryBuilder->select($this->getPrefixedFields(['term_id', 'name', 'slug', 'taxonomy', 'count']));
        }

        return $queryBuilder;
    }

    private function getPrefixedFields(array $fields): array
    {
        $output = [];

        foreach ($fields as $field) {
            $output[] = match ($field) {
                'term_id', 'name', 'slug' => sprintf('p.%s', $field),
                'taxonomy', 'count' => sprintf('tt.%s', $field),
            };
        }

        return $output;
    }
}
