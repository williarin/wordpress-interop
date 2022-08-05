<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
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
            $extraFields = array_diff(
                $queryBuilder->getQueryPart('select'),
                $this->getPrefixedFields(['term_id', 'name', 'slug', 'taxonomy', 'term_taxonomy_id', 'count']),
            );

            $queryBuilder->select([
                ...$this->getPrefixedFields(['term_id', 'name', 'slug', 'taxonomy', 'term_taxonomy_id', 'count']),
                ...$extraFields,
            ]);
        } else {
            foreach ($this->getPrefixedFields($queryBuilder->getQueryPart('select')) as $field) {
                if (!\in_array($field, $queryBuilder->getQueryPart('groupBy'), true)) {
                    $queryBuilder->addGroupBy($field);
                }
            }
        }

        return $queryBuilder;
    }

    public function addTermsToEntity(BaseEntity $entity, array $terms): void
    {
        foreach ($terms as $term) {
            if ($term->termTaxonomyId === null) {
                continue;
            }

            try {
                $this->entityManager->getConnection()
                    ->createQueryBuilder()
                    ->insert($this->entityManager->getTablesPrefix() . 'term_relationships')
                    ->values([
                        'object_id' => '?',
                        'term_taxonomy_id' => '?',
                        'term_order' => '0',
                    ])
                    ->setParameters([$entity->id, (int) $term->termTaxonomyId])
                    ->executeStatement()
                ;
            } catch (UniqueConstraintViolationException) {
            }
        }

        $this->recountTerms();
    }

    public function removeTermsFromEntity(BaseEntity $entity, array $terms): void
    {
        foreach ($terms as $term) {
            if ($term->termTaxonomyId === null) {
                continue;
            }

            $this->entityManager->getConnection()
                ->createQueryBuilder()
                ->delete($this->entityManager->getTablesPrefix() . 'term_relationships')
                ->where('object_id = ?')
                ->andWhere('term_taxonomy_id = ?')
                ->setParameters([$entity->id, (int) $term->termTaxonomyId])
                ->executeStatement()
            ;
        }

        $this->recountTerms();
    }

    private function recountTerms(): void
    {
        $subSelect = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select('COUNT(*)')
            ->from($this->entityManager->getTablesPrefix() . 'term_relationships', 'tr')
            ->leftJoin('tr', $this->entityManager->getTablesPrefix() . 'posts', 'p', 'p.id = tr.object_id')
            ->where('tr.term_taxonomy_id = tt.term_taxonomy_id')
            ->andWhere("tt.taxonomy NOT IN ('link_category')")
            ->andWhere("p.post_status IN ('publish', 'future')")
        ;

        $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->update($this->entityManager->getTablesPrefix() . 'term_taxonomy', 'tt')
            ->set('count', sprintf('(%s)', $subSelect->getSQL()))
            ->executeStatement()
        ;
    }

    private function getPrefixedFields(array $fields): array
    {
        $output = [];

        foreach ($fields as $field) {
            $output[] = match ($field) {
                'term_id', 'name', 'slug' => sprintf('p.%s', $field),
                'taxonomy', 'count', 'term_taxonomy_id' => sprintf('tt.%s', $field),
                default => $field,
            };
        }

        return $output;
    }
}
