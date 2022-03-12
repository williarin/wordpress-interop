<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\Criteria\NestedCondition;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Criteria\TermRelationshipCondition;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\InvalidOrderByOrientationException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
use function Symfony\Component\String\u;
use function Williarin\WordpressInterop\Util\String\property_to_field;
use function Williarin\WordpressInterop\Util\String\select_from_eav;

abstract class AbstractEntityRepository implements EntityRepositoryInterface
{
    use NestedCriteriaTrait;
    use FindByTrait;

    protected const MAPPED_FIELDS = [];
    protected const TABLE_NAME = 'posts';
    protected const TABLE_META_NAME = 'postmeta';
    protected const TABLE_IDENTIFIER = 'id';
    protected const TABLE_META_IDENTIFIER = 'post_id';
    protected const FALLBACK_ENTITY = BaseEntity::class;
    protected const IS_SPECIAL_CRITERIA = 1;

    protected EntityManagerInterface $entityManager;
    protected SerializerInterface $serializer;
    protected PropertyNormalizer $propertyNormalizer;

    public function __construct(
        private string $entityClassName,
    ) {
        $this->propertyNormalizer = new PropertyNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
    }

    public function __call(string $name, array $arguments): mixed
    {
        if (str_starts_with($name, 'findOneBy')) {
            return $this->doFindOneBy($name, $arguments);
        }

        if (str_starts_with($name, 'findBy')) {
            return $this->doFindBy($name, $arguments);
        }

        if (str_starts_with($name, 'update')) {
            return $this->doUpdate($name, $arguments);
        }

        throw new MethodNotFoundException(static::class, $name);
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function updateSingleField(int $id, string $field, mixed $newValue): bool
    {
        $value = $this->normalize($field, $newValue);

        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->update($this->entityManager->getTablesPrefix() . static::TABLE_NAME)
            ->set($field, ':value')
            ->where(sprintf('%s = :id', static::TABLE_IDENTIFIER))
            ->setParameters([
                'id' => $id,
                'key' => strtolower($field),
                'value' => $value,
            ])
            ->executeStatement()
        ;

        return $affectedRows > 0;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function createFindByQueryBuilder(array $criteria, ?array $orderBy): QueryBuilder
    {
        $normalizedCriteria = $this->normalizeCriteria($criteria);

        $queryBuilder = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select($this->getPrefixedEntityBaseFields('p'))
            ->from($this->entityManager->getTablesPrefix() . static::TABLE_NAME, 'p')
        ;

        if (is_subclass_of($this->getEntityClassName(), BaseEntity::class)) {
            $queryBuilder->where('post_type = :post_type')
                ->setParameter('post_type', $this->getPostType())
            ;
        }

        $this->addSelectForExtraFields($queryBuilder);

        foreach ($normalizedCriteria as $field => $value) {
            if ($this->handleSpecialCriteria($queryBuilder, $criteria, $field, $value) === self::IS_SPECIAL_CRITERIA) {
                continue;
            }

            $this->handleRegularCriteria($queryBuilder, $criteria, $field, $value);
        }

        $this->addOrderByClause($queryBuilder, $orderBy);

        return $queryBuilder;
    }

    protected function normalize(string $field, mixed $value): string
    {
        $value = $this->validateFieldValue($field, $value);

        return (string) $this->serializer->normalize($value);
    }

    protected function getPostType(): string
    {
        return 'post';
    }

    protected function doUpdate(string $name, array $arguments): bool
    {
        $resolver = (new OptionsResolver())
            ->setRequired(['0', '1'])
            ->setAllowedTypes('0', 'int')
            ->setInfo('0', 'The ID of the entity to update.')
            ->setInfo('1', 'The new value.')
        ;

        $arguments = $this->validateArguments($resolver, $arguments);

        return $this->updateSingleField($arguments[0], property_to_field(substr($name, 6)), $arguments[1]);
    }

    protected function getMappedMetaKey(mixed $fieldName): string
    {
        if (
            !is_array(static::MAPPED_FIELDS)
            || empty(static::MAPPED_FIELDS)
            || !in_array($fieldName, static::MAPPED_FIELDS, true)
        ) {
            return $fieldName;
        }

        $key = array_search($fieldName, static::MAPPED_FIELDS, true);

        if (is_numeric($key)) {
            return sprintf('_%s', $fieldName);
        }

        return $key;
    }

    protected function addSelectForExtraFields(QueryBuilder $queryBuilder): void
    {
        $extraFields = $this->getEntityExtraFields();

        if (!empty($extraFields)) {
            $queryBuilder->leftJoin(
                'p',
                $this->entityManager->getTablesPrefix() . static::TABLE_META_NAME,
                'pm_self',
                sprintf('p.%s = pm_self.%s', static::TABLE_IDENTIFIER, static::TABLE_META_IDENTIFIER),
            );

            foreach ($extraFields as $extraField) {
                $fieldName = property_to_field($extraField);
                $mappedMetaKey = $this->getMappedMetaKey($fieldName);
                $queryBuilder->addSelect(select_from_eav($fieldName, $mappedMetaKey));
            }

            $queryBuilder->addGroupBy(sprintf('p.%s', static::TABLE_IDENTIFIER));
        }
    }

    protected function handleSpecialCriteria(
        QueryBuilder $queryBuilder,
        array $criteria,
        int|string $field,
        mixed $value,
    ): int {
        if ($value instanceof SelectColumns) {
            $this->selectColumns($queryBuilder, $this->getEntityExtraFields(), $value);

            return self::IS_SPECIAL_CRITERIA;
        }

        if ($value instanceof NestedCondition) {
            $this->createNestedCriteria($queryBuilder, $criteria[$field]->getCriteria(), $value);

            return self::IS_SPECIAL_CRITERIA;
        }

        if ($value instanceof RelationshipCondition) {
            $this->createRelationshipCriteria($queryBuilder, $value);

            return self::IS_SPECIAL_CRITERIA;
        }

        if ($value instanceof TermRelationshipCondition) {
            $this->createTermRelationshipCriteria($queryBuilder, $value);

            return self::IS_SPECIAL_CRITERIA;
        }

        return 0;
    }

    protected function handleRegularCriteria(
        QueryBuilder $queryBuilder,
        array $criteria,
        string $field,
        mixed $value,
    ): void {
        $snakeField = u($field)
            ->snake()
            ->toString()
        ;
        $parameter = ":{$snakeField}";
        $operator = '=';

        if ($criteria[$field] instanceof Operand) {
            $operator = $criteria[$field]->getOperator();

            if ($operator === Operand::OPERATOR_IN) {
                $operator = 'IN';
                $parameters = array_map(
                    static fn (int $number) => "{$snakeField}_{$number}",
                    range(0, count($value) - 1),
                );
                $parameter = sprintf('(:%s)', implode(', :', $parameters));

                foreach ($parameters as $index => $name) {
                    $queryBuilder->setParameter($name, $value[$index]);
                }
            } else {
                $queryBuilder->setParameter($snakeField, $criteria[$field]->getOperand());
            }
        } else {
            $queryBuilder->setParameter($snakeField, $value);
        }

        $expr = sprintf('%s %s %s', $field, $operator, $parameter);

        if (in_array(substr($field, (strpos($field, '.') ?: -1) + 1), $this->getEntityExtraFields(), true)) {
            $queryBuilder->andHaving($expr);
        } else {
            $queryBuilder->andWhere($expr);
        }
    }

    protected function addOrderByClause(QueryBuilder $queryBuilder, ?array $orderBy): void
    {
        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $orientation) {
                $this->validateFieldName($field, static::FALLBACK_ENTITY, static::TABLE_NAME);

                if (!in_array(strtolower($orientation), ['asc', 'desc'], true)) {
                    throw new InvalidOrderByOrientationException($orientation);
                }

                $queryBuilder->addOrderBy($field, $orientation);
            }
        }
    }

    private function createRelationshipCriteria(
        QueryBuilder $queryBuilder,
        RelationshipCondition $condition
    ): void {
        static $aliasNumber = 0;
        $alias = sprintf('pm_relation_%d', $aliasNumber++);

        $queryBuilder->leftJoin(
            'p',
            $this->entityManager->getTablesPrefix() . static::TABLE_META_NAME,
            $alias,
            sprintf('p.%s = %s.meta_value', static::TABLE_IDENTIFIER, $alias),
        )
            ->andWhere(sprintf('%s.%s = :%s_id', $alias, static::TABLE_META_IDENTIFIER, $alias))
            ->andWhere(sprintf('%s.meta_key = :%s_field', $alias, $alias))
            ->setParameter(sprintf('%s_id', $alias), $condition->getRelationshipId())
            ->setParameter(sprintf('%s_field', $alias), $condition->getRelationshipFieldName())
        ;
    }

    private function createTermRelationshipCriteria(
        QueryBuilder $queryBuilder,
        TermRelationshipCondition $condition,
    ): void {
        static $aliasNumber = 0;

        $queryBuilder
            ->join(
                'p',
                $this->entityManager->getTablesPrefix() . 'term_relationships',
                sprintf('tr_%d', $aliasNumber),
                sprintf('p.%s = tr_%s.object_id', static::TABLE_IDENTIFIER, $aliasNumber),
            )
            ->join(
                sprintf('tr_%d', $aliasNumber),
                $this->entityManager->getTablesPrefix() . 'term_taxonomy',
                sprintf('tt_%d', $aliasNumber),
                sprintf('tr_%d.term_taxonomy_id = tt_%d.term_taxonomy_id', $aliasNumber, $aliasNumber),
            )
            ->join(
                sprintf('tt_%d', $aliasNumber),
                $this->entityManager->getTablesPrefix() . 'terms',
                sprintf('t_%d', $aliasNumber),
                sprintf('tt_%d.term_id = t_%d.term_id', $aliasNumber, $aliasNumber),
            )
        ;

        $prefixedCriteria = $this->getPrefixedCriteriaForTermRelationshipCondition(
            $condition->getCriteria(),
            $aliasNumber,
        );

        foreach ($prefixedCriteria as $field => $value) {
            $this->handleRegularCriteria($queryBuilder, $prefixedCriteria, $field, $value);
        }

        ++$aliasNumber;
    }

    private function getPrefixedCriteriaForTermRelationshipCondition(array $criteria, int $aliasNumber): array
    {
        $output = [];

        foreach ($criteria as $field => $value) {
            $prefixedField = match ($field) {
                'term_id', 'name', 'slug', 'term_group' => sprintf('t_%d.%s', $aliasNumber, $field),
                'taxonomy', 'description', 'count' => sprintf('tt_%d.%s', $aliasNumber, $field),
            };

            $output[$prefixedField] = $value;
        }

        return $output;
    }

    private function selectColumns(QueryBuilder $queryBuilder, array $extraFields, SelectColumns $value): void
    {
        $selects = [];
        $hasExtraFields = false;

        foreach ($value->getColumns() as $column) {
            if (in_array($column, $extraFields, true)) {
                $mappedMetaKey = $this->getMappedMetaKey($column);
                $selects[] = select_from_eav($column, $mappedMetaKey);
                $hasExtraFields = true;
            } elseif (str_starts_with($column, 'MAX(')) {
                $selects[] = $column;
                $hasExtraFields = true;
            } else {
                $selects[] = $column;
                $queryBuilder->addGroupBy(($alias = strrchr($column, ' ')) ? substr($alias, 1) : $column);
            }
        }

        $queryBuilder->select(...$selects);

        if (!$hasExtraFields) {
            $queryBuilder->resetQueryPart('groupBy');

            $joinQueryPart = $queryBuilder->getQueryPart('join');
            $queryBuilder->resetQueryPart('join');

            foreach ($joinQueryPart['p'] as $index => $part) {
                if ($part['joinAlias'] === 'pm_self') {
                    unset($joinQueryPart['p'][$index]);
                }
            }

            foreach ($joinQueryPart as $alias => $parts) {
                foreach ($parts as $part) {
                    $method = match ($part['joinType']) {
                        'left' => 'leftJoin',
                        'right' => 'rightJoin',
                        default => 'join',
                    };

                    $queryBuilder->{$method}($alias, $part['joinTable'], $part['joinAlias'], $part['joinCondition']);
                }
            }
        }
    }
}
