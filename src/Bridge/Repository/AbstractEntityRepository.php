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
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\InvalidOrderByOrientationException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
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

            $queryBuilder->groupBy('p.ID');
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

        return 0;
    }

    protected function handleRegularCriteria(
        QueryBuilder $queryBuilder,
        array $criteria,
        string $field,
        mixed $value,
    ): void {
        $expr = sprintf(
            '`%s` %s :%s',
            $field,
            $criteria[$field] instanceof Operand ? $criteria[$field]->getOperator() : '=',
            $field,
        );

        if (in_array($field, $this->getEntityExtraFields(), true)) {
            $queryBuilder->andHaving($expr);
        } else {
            $queryBuilder->andWhere($expr);
        }

        $queryBuilder->setParameter(
            $field,
            $criteria[$field] instanceof Operand ? $criteria[$field]->getOperand() : $value
        );
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
            $this->entityManager->getTablesPrefix() . 'postmeta',
            $alias,
            sprintf('p.ID = %s.meta_value', $alias),
        )
            ->andWhere(sprintf('%s.post_id = :%s_id', $alias, $alias))
            ->andWhere(sprintf('%s.meta_key = :%s_field', $alias, $alias))
            ->setParameter(sprintf('%s_id', $alias), $condition->getRelationshipId())
            ->setParameter(sprintf('%s_field', $alias), $condition->getRelationshipFieldName())
        ;
    }

    private function selectColumns(QueryBuilder $queryBuilder, array $extraFields, SelectColumns $value): void
    {
        $selects = [];

        foreach ($value->getColumns() as $column) {
            if (in_array($column, $extraFields, true)) {
                $mappedMetaKey = $this->getMappedMetaKey($column);
                $selects[] = select_from_eav($column, $mappedMetaKey);
            } else {
                $selects[] = $column;
            }
        }

        $queryBuilder->select(...$selects);
    }
}
