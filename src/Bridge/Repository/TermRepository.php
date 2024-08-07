<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\Bridge\Entity\Term;
use Williarin\WordpressInterop\Bridge\Entity\TermTaxonomy;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;

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

        preg_match('/SELECT (.*) FROM/im', $queryBuilder->getSQL(), $match);
        $selectedFields = explode(', ', $match[1] ?? '');

        preg_match('/GROUP BY (.*)\s?(?:HAVING|ORDER|LIMIT|OFFSET|FETCH|$)/im', $queryBuilder->getSQL(), $match);
        $groupByFields = explode(', ', $match[1] ?? '');

        if (\count(array_filter($criteria, static fn ($condition) => $condition instanceof SelectColumns)) === 0) {
            $extraFields = array_diff(
                $selectedFields,
                $this->getPrefixedFields(['term_id', 'name', 'slug', 'taxonomy', 'term_taxonomy_id', 'count']),
            );

            $queryBuilder->select(
                ...$this->getPrefixedFields(['term_id', 'name', 'slug', 'taxonomy', 'term_taxonomy_id', 'count']),
                ...$extraFields
            );
        } else {
            foreach ($this->getPrefixedFields($selectedFields) as $field) {
                if (!\in_array($field, $groupByFields, true)) {
                    $queryBuilder->addGroupBy($field);
                }
            }
        }

        return $queryBuilder;
    }

    public function createTermForTaxonomy(string $termName, string $taxonomy): Term
    {
        try {
            $term = $this->findOneBy([
                'name' => $termName,
                'taxonomy' => $taxonomy
            ]);
        } catch (EntityNotFoundException) {
            $term = (new Term())
                ->setName($termName)
                ->setSlug((new AsciiSlugger())->slug($termName)->lower()->toString())
            ;

            $this->persist($term);

            $termTaxonomy = (new TermTaxonomy())
                ->setTermId($term->termId)
                ->setTaxonomy($taxonomy)
            ;

            $this->entityManager->getRepository(TermTaxonomy::class)
                ->persist($termTaxonomy)
            ;

            $term = $this->findOneBy([
                'name' => $termName,
                'taxonomy' => $taxonomy
            ]);
        }

        return $term;
    }

    /**
     * @param Term[] $terms
     */
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

    /**
     * @param Term[] $terms
     */
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
        $tableName = $this->entityManager->getTablesPrefix() . 'term_taxonomy';

        $subSelect = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select('COUNT(*)')
            ->from($this->entityManager->getTablesPrefix() . 'term_relationships', 'tr')
            ->leftJoin('tr', $this->entityManager->getTablesPrefix() . 'posts', 'p', 'p.id = tr.object_id')
            ->where("tr.term_taxonomy_id = $tableName.term_taxonomy_id")
            ->andWhere("$tableName.taxonomy NOT IN ('link_category')")
            ->andWhere("p.post_status IN ('publish', 'future')")
        ;

        $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->update($tableName)
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
