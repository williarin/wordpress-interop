<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\Term;
use Williarin\WordpressInterop\Criteria\NestedCondition;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\InvalidOrderByOrientationException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;

final class TermRepository implements RepositoryInterface
{
    use NestedCriteriaTrait;
    use FindByTrait;

    private const TABLE_NAME = 'terms';

    public function __call(string $name, array $arguments): Term|array|bool
    {
        if (str_starts_with($name, 'findOneBy')) {
            return $this->doFindOneBy($name, $arguments);
        }

        if (str_starts_with($name, 'findBy')) {
            return $this->doFindBy($name, $arguments);
        }

        throw new MethodNotFoundException(self::class, $name);
    }

    public function createFindByQueryBuilder(array $criteria, ?array $orderBy): QueryBuilder
    {
        $normalizedCriteria = $this->normalizeCriteria($criteria);

        $queryBuilder = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select($this->getPrefixedFields(['term_id', 'name', 'slug', 'taxonomy', 'count']))
            ->from($this->entityManager->getTablesPrefix() . 'terms', 't')
            ->innerJoin('t', $this->entityManager->getTablesPrefix() . 'term_taxonomy', 'tt', 't.term_id = tt.term_id')
        ;

        foreach ($normalizedCriteria as $field => $value) {
            if ($value instanceof SelectColumns) {
                $queryBuilder->select(...$this->getPrefixedFields($value->getColumns()));

                continue;
            }

            if ($value instanceof NestedCondition) {
                $this->createNestedCriteria($queryBuilder, $criteria[$field]->getCriteria(), $value);

                continue;
            }

            $expr = sprintf(
                '%s %s :%s',
                current($this->getPrefixedFields([$field])),
                $criteria[$field] instanceof Operand ? $criteria[$field]->getOperator() : '=',
                $field,
            );

            $queryBuilder->andWhere($expr)
                ->setParameter(
                    $field,
                    $criteria[$field] instanceof Operand ? $criteria[$field]->getOperand() : $value
                )
            ;
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $orientation) {
                $this->validateFieldName($field, Term::class, self::TABLE_NAME);

                if (!in_array(strtolower($orientation), ['asc', 'desc'], true)) {
                    throw new InvalidOrderByOrientationException($orientation);
                }

                $queryBuilder->addOrderBy($field, $orientation);
            }
        }

        return $queryBuilder;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function getEntityClassName(): string
    {
        return Term::class;
    }

    private function getPrefixedFields(array $fields): array
    {
        $output = [];

        foreach ($fields as $field) {
            $output[] = match ($field) {
                'term_id', 'name', 'slug' => sprintf('t.%s', $field),
                'taxonomy', 'count' => sprintf('tt.%s', $field),
            };
        }

        return $output;
    }
}
