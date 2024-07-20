<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Doctrine\DBAL\Query\Expression\CompositeExpression;
use Doctrine\DBAL\Query\QueryBuilder;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\Bridge\Entity\PostMeta;
use Williarin\WordpressInterop\Criteria\NestedCondition;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\PostRelationshipCondition;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Criteria\TermRelationshipCondition;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\InvalidEntityException;
use Williarin\WordpressInterop\Exception\InvalidOrderByOrientationException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
use function Symfony\Component\String\u;
use function Williarin\WordpressInterop\Util\String\field_to_property;
use function Williarin\WordpressInterop\Util\String\property_to_field;
use function Williarin\WordpressInterop\Util\String\select_from_eav;

/**
 * @method array getMappedFields()
 */
abstract class AbstractEntityRepository implements EntityRepositoryInterface
{
    use FindByTrait;
    use EntityPropertiesTrait;

    /** @deprecated Implement getMappedFields() method instead */
    protected const MAPPED_FIELDS = [];
    protected const TABLE_NAME = 'posts';
    protected const TABLE_META_NAME = 'postmeta';
    protected const META_ENTITY_CLASS_NAME = PostMeta::class;
    protected const TABLE_IDENTIFIER = 'id';
    protected const TABLE_META_IDENTIFIER = 'post_id';
    protected const FALLBACK_ENTITY = BaseEntity::class;
    protected const IS_SPECIAL_CRITERIA = 1;

    protected EntityManagerInterface $entityManager;
    protected SerializerInterface $serializer;
    protected PropertyNormalizer $propertyNormalizer;
    #[ArrayShape([
        'allow_extra_properties' => 'bool',
    ])]
    protected array $options;

    private array $tableAliases = [];
    private array $additionalFieldsToSelect = [];

    public function __construct(
        private string $entityClassName,
    ) {
        $this->propertyNormalizer = new PropertyNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $this->setOptions([]);
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

    public function getMetaEntityClassName(): string
    {
        return static::META_ENTITY_CLASS_NAME;
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    public function setOptions(array $options): EntityRepositoryInterface
    {
        $resolver = (new OptionsResolver())
            ->setDefaults([
                'allow_extra_properties' => false,
            ])
            ->setAllowedTypes('allow_extra_properties', 'bool')
        ;

        $this->options = $resolver->resolve($options);

        return $this;
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

    public function createFindByQueryBuilder(array $criteria, ?array $orderBy): QueryBuilder
    {
        $normalizedCriteria = $this->normalizeCriteria($criteria);
        $this->tableAliases['p'] = $this->getEntityBaseFields();

        $prefixedEntityBaseFields = $this->getPrefixedEntityBaseFields('p');

        $hasSelectColumns = array_filter(
            $normalizedCriteria,
            static fn (mixed $value) => $value instanceof SelectColumns,
        );

        $queryBuilder = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->from($this->entityManager->getTablesPrefix() . static::TABLE_NAME, 'p')
            ->addGroupBy(...$prefixedEntityBaseFields)
        ;

        if (is_subclass_of($this->getEntityClassName(), BaseEntity::class)) {
            $queryBuilder->where('p.post_type = :post_type')
                ->setParameter('post_type', $this->getPostType())
            ;
        }

        if (!$hasSelectColumns) {
            $queryBuilder->select(...$prefixedEntityBaseFields);
            $this->addSelectForExtraFields($queryBuilder);
        }

        foreach ($normalizedCriteria as $field => $value) {
            if ($this->handleSpecialCriteria($queryBuilder, $criteria, $field, $value) === self::IS_SPECIAL_CRITERIA) {
                continue;
            }

            $this->handleRegularCriteria($queryBuilder, $criteria, $field, $value);
        }

        $this->addOrderByClause($queryBuilder, $orderBy);

        $queryBuilder->addSelect(...$this->additionalFieldsToSelect);
        $this->additionalFieldsToSelect = [];

        return $queryBuilder;
    }

    public function persist(mixed $entity): void
    {
        if ($entity::class !== $this->getEntityClassName() && !is_subclass_of($entity, $this->getEntityClassName())) {
            throw new InvalidEntityException($entity, $this->getEntityClassName());
        }

        $baseFields = $this->getEntityBaseFields($entity::class);

        $baseFields = array_combine($baseFields, array_map(
            fn (mixed $value, string $field) => $this->normalize($field, $entity->{field_to_property($field)}),
            array_flip($baseFields),
            $baseFields,
        ));

        unset($baseFields[static::TABLE_IDENTIFIER]);

        $identifierProperty = field_to_property(static::TABLE_IDENTIFIER);

        if ($entity->{$identifierProperty} === null) {
            $this->entityManager->getConnection()
                ->createQueryBuilder()
                ->insert($this->entityManager->getTablesPrefix() . static::TABLE_NAME)
                ->values(array_combine(array_keys($baseFields), array_fill(0, count($baseFields), '?')))
                ->setParameters(array_values($baseFields))
                ->executeStatement()
            ;

            $entity->{$identifierProperty} = (int) $this->entityManager->getConnection()
                ->lastInsertId();
        } else {
            $queryBuilder = $this->entityManager->getConnection()
                ->createQueryBuilder()
                ->update($this->entityManager->getTablesPrefix() . static::TABLE_NAME)
                ->where(static::TABLE_IDENTIFIER . ' = :' . static::TABLE_IDENTIFIER)
                ->setParameter(static::TABLE_IDENTIFIER, $entity->{static::TABLE_IDENTIFIER})
            ;

            foreach ($baseFields as $field => $value) {
                $queryBuilder
                    ->set($field, ":{$field}")
                    ->setParameter($field, $value)
                ;
            }

            $queryBuilder->executeStatement();
        }
    }

    public function getMappedMetaKey(string $fieldName, string $entityClassName = null): string
    {
        $targetClass = $entityClassName ? $this->entityManager->getRepository($entityClassName) : $this;

        if (method_exists($targetClass, 'getMappedFields')) {
            $mappedFields = $targetClass->getMappedFields();
        } else {
            // BC layer, to be removed when MAPPED_FIELDS constant is removed
            $mappedFields = (new \ReflectionClassConstant($targetClass, 'MAPPED_FIELDS'))->getValue();
        }

        if (
            !is_array($mappedFields)
            || empty($mappedFields)
            || !in_array($fieldName, $mappedFields, true)
        ) {
            return $fieldName;
        }

        $key = array_search($fieldName, $mappedFields, true);

        if (is_numeric($key)) {
            return sprintf('_%s', $fieldName);
        }

        return $key;
    }

    public function isFieldMapped(string $fieldName, string $entityClassName = null): bool
    {
        $targetClass = $entityClassName ? $this->entityManager->getRepository($entityClassName) : $this;

        if (method_exists($targetClass, 'getMappedFields')) {
            $mappedFields = $targetClass->getMappedFields();
        } else {
            // BC layer, to be removed when MAPPED_FIELDS constant is removed
            $mappedFields = (new \ReflectionClassConstant($targetClass, 'MAPPED_FIELDS'))->getValue();
        }

        if (
            !is_array($mappedFields)
            || empty($mappedFields)
            || !in_array($fieldName, $mappedFields, true)
        ) {
            return false;
        }

        return true;
    }

    public function getTableAliasForField(string $fieldName): ?string
    {
        $mappedField = $this->getMappedMetaKey($fieldName);

        foreach ($this->tableAliases as $alias => $fields) {
            if (in_array($mappedField, $fields, true)) {
                return $alias;
            }
        }

        return null;
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

    protected function addSelectForExtraFields(QueryBuilder $queryBuilder): void
    {
        $extraFields = $this->getEntityExtraFields();

        if (!empty($extraFields)) {
            $this->joinSelfMetaTable($queryBuilder);

            foreach ($extraFields as $extraField) {
                $fieldName = property_to_field($extraField);
                $mappedMetaKey = $this->getMappedMetaKey($fieldName);
                $this->tableAliases['pm_self'][] = $fieldName;
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
            $this->createNestedCriteria($queryBuilder, $criteria[$field]);

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

        if ($value instanceof PostRelationshipCondition) {
            $this->createPostRelationshipCriteria($queryBuilder, $value);

            return self::IS_SPECIAL_CRITERIA;
        }

        return 0;
    }

    protected function handleRegularCriteria(
        QueryBuilder $queryBuilder,
        array $criteria,
        string $field,
        mixed $value,
        string $entityClassName = null,
        int $aliasNumber = null,
    ): void {
        $queryBuilder->andWhere($this->getWhereExpressionFromCriteriaField(
            $queryBuilder,
            $criteria,
            $field,
            $value,
            $entityClassName,
            $aliasNumber,
        ));
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

    private function getWhereExpressionFromCriteriaField(
        QueryBuilder $queryBuilder,
        array $criteria,
        string $field,
        mixed $value,
        string $entityClassName = null,
        int $aliasNumber = null,
    ): CompositeExpression {
        $snakeField = str_replace('.', '_', u($field) ->snake() ->toString());
        $parameter = ":{$snakeField}";
        $operator = '=';
        $prefixedField = $field;
        $expression = $queryBuilder->expr();

        if ($criteria[$field] instanceof Operand) {
            ['operator' => $operator, 'parameter' => $parameter] =
                $this->flattenOperand($queryBuilder, $criteria[$field], $field, $value);
        } else {
            $queryBuilder->setParameter($snakeField, $value);
        }

        foreach ($this->tableAliases as $alias => $fields) {
            if (in_array($field, $fields, true)) {
                $prefixedField = sprintf('%s.%s', $alias, $field);
            }
        }

        if (
            in_array(
                substr($field, (strpos($field, '.') ?: -1) + 1),
                $this->getEntityExtraFields($entityClassName),
                true,
            )
        ) {
            $metaAlias = sprintf('pm_%s', $aliasNumber ?? 'self');

            if ($aliasNumber === null) {
                $metaAlias = $this->joinSelfMetaTable($queryBuilder, true);
                $this->tableAliases[$metaAlias][] = $field;
            }

            if ($this->isFieldMapped($field, $entityClassName)) {
                $exprKey = sprintf('%s.meta_key = :%s_key', $metaAlias, $snakeField);
                $queryBuilder->setParameter(
                    sprintf('%s_key', $snakeField),
                    $this->getMappedMetaKey($field, $entityClassName),
                );
            } else {
                $exprKey = sprintf("TRIM(LEADING '_' FROM %s.meta_key) = :%s_key", $metaAlias, $snakeField);
                $queryBuilder->setParameter(sprintf('%s_key', $snakeField), $field);
            }

            $exprValue = trim(sprintf('%s.meta_value %s %s', $metaAlias, $operator, $parameter));

            return $expression->and($exprKey, $exprValue);
        }

        if ($operator === Operand::OPERATOR_IN_ALL) {
            $operator = 'IN';
            $queryBuilder->andHaving(sprintf('COUNT(DISTINCT %s) = :%s_count', $prefixedField, $snakeField))
                ->setParameter(sprintf('%s_count', $snakeField), count($value))
            ;
        }

        return $expression->and(trim(sprintf('%s %s %s', $prefixedField, $operator, $parameter)));
    }

    #[ArrayShape([
        'operator' => 'string',
        'parameter' => 'string',
    ])]
    private function flattenOperand(QueryBuilder $queryBuilder, Operand $operand, string $field, mixed $value): array
    {
        $snakeField = str_replace('.', '_', u($field) ->snake() ->toString());
        $parameter = ":{$snakeField}";
        $operator = $operand->getOperator();

        if (
            in_array($operator, [Operand::OPERATOR_IN, Operand::OPERATOR_NOT_IN, Operand::OPERATOR_IN_ALL], true)
        ) {
            if (count($value) === 0) {
                $value[] = md5((string) time());
            }

            $parameters = array_map(
                static fn (int $number) => "{$snakeField}_{$number}",
                range(0, count($value) - 1),
            );

            $parameter = sprintf('(:%s)', implode(', :', $parameters));
            $listValue = array_values($value);

            foreach ($parameters as $index => $name) {
                $queryBuilder->setParameter($name, $listValue[$index]);
            }
        } else {
            $queryBuilder->setParameter($snakeField, $operand->getOperand());
        }

        return [
            'operator' => $operator,
            'parameter' => $operand->isStandaloneOperator() ? null : $parameter,
        ];
    }

    private function createNestedCriteria(QueryBuilder $queryBuilder, NestedCondition $condition): void
    {
        $criteria = $condition->getCriteria();
        $normalizedCriteria = $this->normalizeCriteria($condition->getCriteria());
        $expressions = [];

        foreach ($normalizedCriteria as $field => $value) {
            $expressions[] = $this->getWhereExpressionFromCriteriaField($queryBuilder, $criteria, $field, $value);
        }

        $queryBuilder->andWhere($queryBuilder->expr()->{$condition->getOperator()}(...$expressions));
    }

    private function joinSelfMetaTable(QueryBuilder $queryBuilder, bool $incrementIfNecessary = false): string
    {
        $applyJoin = function (QueryBuilder $queryBuilder, string $alias): void {
            if (!array_key_exists($alias, $this->tableAliases)) {
                $this->tableAliases[$alias] = [];
            }

            $queryBuilder->leftJoin(
                'p',
                $this->entityManager->getTablesPrefix() . static::TABLE_META_NAME,
                $alias,
                sprintf('p.%s = %s.%s', static::TABLE_IDENTIFIER, $alias, static::TABLE_META_IDENTIFIER),
            );
        };

        static $aliasNumber = 1;
        $alias = 'pm_self';

        if (!$incrementIfNecessary && $this->hasJoin($queryBuilder, $alias)) {
            return $alias;
        }

        if (!$this->hasJoin($queryBuilder, $alias)) {
            $applyJoin($queryBuilder, $alias);

            return $alias;
        }

        $alias .= '_' . $aliasNumber++;
        $applyJoin($queryBuilder, $alias);

        return $alias;
    }

    private function hasJoin(QueryBuilder $queryBuilder, string $joinAlias): bool
    {
        if (preg_match_all('/JOIN (\w+) (\w+) ON p\./im', $queryBuilder->getSQL(), $matches, PREG_SET_ORDER)) {
            return count(array_filter($matches, static fn (array $match) => $match[2] === $joinAlias)) > 0;
        }

        return false;
    }

    private function createRelationshipCriteria(
        QueryBuilder $queryBuilder,
        RelationshipCondition $condition
    ): void {
        static $aliasNumber = 0;
        $alias = sprintf('pm_relation_%d', $aliasNumber++);
        $snakeField = u($condition->getRelationshipFieldName())
            ->snake()
            ->toString()
        ;
        $parameter = ":{$snakeField}";
        $operator = '=';

        if (($operand = $condition->getRelationshipIdOrOperand()) instanceof Operand) {
            $operator = $operand->getOperator();
            $value = $operand->getOperand();

            if (in_array($operator, [Operand::OPERATOR_IN, Operand::OPERATOR_NOT_IN], true)) {
                $parameters = array_map(
                    static fn (int $number) => "{$snakeField}_{$number}",
                    range(0, count($value) - 1),
                );
                $parameter = sprintf('(:%s)', implode(', :', $parameters));

                foreach ($parameters as $index => $name) {
                    $queryBuilder->setParameter($name, $value[$index]);
                }
            } else {
                $queryBuilder->setParameter($snakeField, $value);
            }
        } else {
            $queryBuilder->setParameter($snakeField, $condition->getRelationshipIdOrOperand());
        }

        $this->tableAliases[$alias] = [$condition->getRelationshipFieldName()];

        $queryBuilder->leftJoin(
            'p',
            $this->entityManager->getTablesPrefix() . static::TABLE_META_NAME,
            $alias,
            sprintf('p.%s = %s.meta_value', static::TABLE_IDENTIFIER, $alias),
        )
            ->andWhere(sprintf('%s.%s %s %s', $alias, static::TABLE_META_IDENTIFIER, $operator, $parameter))
            ->andWhere(sprintf('%s.meta_key = :%s_field', $alias, $alias))
            ->setParameter(sprintf('%s_field', $alias), $condition->getRelationshipFieldName())
        ;

        if (!empty($condition->getAlias())) {
            $trimmedAlias = trim($condition->getAlias(), '_');

            $this->addEntityExtraField($trimmedAlias);
            $this->additionalFieldsToSelect[] = sprintf(
                "MAX(CASE WHEN %s.meta_key = '%s' THEN %s.%s END) AS `%s`",
                $alias,
                $condition->getRelationshipFieldName(),
                $alias,
                self::TABLE_META_IDENTIFIER,
                $trimmedAlias,
            );
        }
    }

    private function createTermRelationshipCriteria(
        QueryBuilder $queryBuilder,
        TermRelationshipCondition $condition,
    ): void {
        static $aliasNumber = 0;
        static $parameterNumber = 0;

        if (($conditionField = $condition->getJoinConditionField()) === TermRelationshipCondition::IDENTIFIER) {
            $joinCondition = sprintf('p.%s = tr_%s.object_id', static::TABLE_IDENTIFIER, $aliasNumber);
        } else {
            $tableAlias = $this->getTableAliasForField($conditionField) ?? 'pm_self';
            $parameterName = sprintf('param_%s_%s', $aliasNumber, $parameterNumber);
            $mappedKey = $this->getMappedMetaKey($conditionField);
            $joinCondition = sprintf(
                '%s.meta_key = :%s AND %s.%s = tr_%s.object_id',
                $tableAlias,
                $parameterName,
                $tableAlias,
                str_contains($tableAlias, 'relation') ? self::TABLE_META_IDENTIFIER : 'meta_value',
                $aliasNumber,
            );
            $queryBuilder->setParameter($parameterName, $mappedKey);
            ++$parameterNumber;
        }

        $termTableAlias = $condition->getTermTableAlias() ?? sprintf('t_%d', $aliasNumber);

        $queryBuilder
            ->join(
                'p',
                $this->entityManager->getTablesPrefix() . 'term_relationships',
                sprintf('tr_%d', $aliasNumber),
                $joinCondition,
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
                $termTableAlias,
                sprintf('tt_%d.term_id = %s.term_id', $aliasNumber, $termTableAlias),
            )
        ;

        $prefixedCriteria = $this->getPrefixedCriteriaForTermRelationshipCondition(
            $condition->getCriteria(),
            $termTableAlias,
            $aliasNumber,
        );

        $normalizedCriteria = $this->normalizeCriteria($prefixedCriteria, ignoreValidation: true);

        foreach ($normalizedCriteria as $field => $value) {
            if (
                $this->handleSpecialCriteria(
                    $queryBuilder,
                    $prefixedCriteria,
                    $field,
                    $value,
                ) === self::IS_SPECIAL_CRITERIA
            ) {
                continue;
            }

            $this->handleRegularCriteria($queryBuilder, $prefixedCriteria, $field, $value);
        }

        ++$aliasNumber;
    }

    private function createPostRelationshipCriteria(
        QueryBuilder $queryBuilder,
        PostRelationshipCondition $condition,
    ): void {
        static $aliasNumber = 0;

        $queryBuilder
            ->join(
                'tt',
                $this->entityManager->getTablesPrefix() . 'term_relationships',
                sprintf('tr_%d', $aliasNumber),
                sprintf('tt.term_taxonomy_id = tr_%s.term_taxonomy_id', $aliasNumber),
            )
            ->join(
                sprintf('tr_%d', $aliasNumber),
                $this->entityManager->getTablesPrefix() . 'posts',
                sprintf('p_%d', $aliasNumber),
                sprintf('tr_%d.object_id = p_%d.id', $aliasNumber, $aliasNumber),
            )
        ;

        $this->additionalFieldsToSelect[] = 'tt.term_taxonomy_id';
        $this->addPostMetaJoinForPostRelationshipCondition($queryBuilder, $condition, $aliasNumber);
        $prefixedCriteria = $this->getPrefixedCriteriaForPostRelationshipCondition($condition, $aliasNumber);
        $normalizedCriteria = $this->normalizeCriteria($prefixedCriteria, $condition->getEntityClassName());

        foreach ($normalizedCriteria as $field => $value) {
            $this->handleRegularCriteria(
                $queryBuilder,
                $prefixedCriteria,
                $field,
                $value,
                $condition->getEntityClassName(),
                $aliasNumber,
            );
        }

        ++$aliasNumber;
    }

    private function addPostMetaJoinForPostRelationshipCondition(
        QueryBuilder $queryBuilder,
        PostRelationshipCondition $condition,
        int $aliasNumber,
    ): void {
        $extraFields = $this->getEntityExtraFields($condition->getEntityClassName());

        if (!empty($extraFields)) {
            $alias = sprintf('pm_%d', $aliasNumber);
            $this->tableAliases[$alias] = [];

            $queryBuilder->leftJoin(
                sprintf('p_%d', $aliasNumber),
                $this->entityManager->getTablesPrefix() . self::TABLE_META_NAME,
                $alias,
                sprintf('p_%d.id = pm_%d.%s', $aliasNumber, $aliasNumber, self::TABLE_META_IDENTIFIER),
            );

            foreach ($extraFields as $extraField) {
                $this->tableAliases[$alias][] = property_to_field($extraField);
            }

            $queryBuilder->addGroupBy(sprintf('p_%d.id', $aliasNumber));
        }
    }

    private function getPrefixedCriteriaForTermRelationshipCondition(
        array $criteria,
        string $termTableAlias,
        int $aliasNumber,
    ): array {
        $output = [];

        foreach ($criteria as $field => $value) {
            $prefixedField = match ($field) {
                'term_id', 'name', 'slug', 'term_group' => sprintf('%s.%s', $termTableAlias, $field),
                'taxonomy', 'description', 'count' => sprintf('tt_%d.%s', $aliasNumber, $field),
            };

            $output[$prefixedField] = $value;
        }

        return $output;
    }

    private function getPrefixedCriteriaForPostRelationshipCondition(
        PostRelationshipCondition $condition,
        int $aliasNumber
    ): array {
        $output = [];

        foreach ($condition->getCriteria() as $field => $value) {
            $prefixedField = $field;

            if (in_array($field, $this->getEntityBaseFields($condition->getEntityClassName()), true)) {
                $prefixedField = sprintf('p_%d.%s', $aliasNumber, $field);
            }

            $output[$prefixedField] = $value;
        }

        return $output;
    }

    private function selectColumns(QueryBuilder $queryBuilder, array $extraFields, SelectColumns $value): void
    {
        $selects = [];

        $queryBuilder->resetGroupBy();

        foreach ($value->getColumns() as $column) {
            if (in_array($column, $extraFields, true)) {
                $mappedMetaKey = $this->getMappedMetaKey($column);
                $selects[] = select_from_eav($column, $mappedMetaKey);
            } elseif (str_starts_with($column, 'MAX(')) {
                $selects[] = $column;
                $this->joinSelfMetaTable($queryBuilder);
            } else {
                foreach ($this->tableAliases as $alias => $fields) {
                    if (in_array($column, $fields, true)) {
                        $column = sprintf('%s.%s', $alias, $column);
                    }
                }

                $selects[] = $column;
                $queryBuilder->addGroupBy(($alias = strrchr($column, ' ')) ? substr($alias, 1) : $column);
            }
        }

        $queryBuilder->select(...$selects, ...$this->additionalFieldsToSelect);
        $this->additionalFieldsToSelect = [];
    }
}
