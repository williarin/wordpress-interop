<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\Criteria\NestedCondition;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
use Williarin\WordpressInterop\Exception\InvalidOrderByOrientationException;
use Williarin\WordpressInterop\Exception\InvalidTypeException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
use function Williarin\WordpressInterop\Util\String\field_to_property;
use function Williarin\WordpressInterop\Util\String\property_to_field;
use function Williarin\WordpressInterop\Util\String\select_from_eav;

/**
 * @method BaseEntity   findOneByPostAuthor(int|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostDate(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostDateGmt(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostContent(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostTitle(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostExcerpt(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostStatus(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByCommentStatus(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPingStatus(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostPassword(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostName(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByToPing(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPinged(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostModifiedGmt(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostParent(int|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByGuid(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByMenuOrder(int|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostType(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostMimeType(string|Operand $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByCommentCount(int|Operand $newValue, array $orderBy = null)
 * @method BaseEntity[] findByPostAuthor(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostDate(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostDateGmt(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostContent(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostTitle(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostExcerpt(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByCommentStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPingStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostPassword(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostName(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByToPing(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPinged(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostModifiedGmt(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostParent(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByGuid(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByMenuOrder(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostMimeType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByCommentCount(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method bool         updatePostAuthor(int $id, int|Operand $newValue)
 * @method bool         updatePostDate(int $id, DateTimeInterface|Operand $newValue)
 * @method bool         updatePostDateGmt(int $id, DateTimeInterface|Operand $newValue)
 * @method bool         updatePostContent(int $id, string|Operand $newValue)
 * @method bool         updatePostTitle(int $id, string|Operand $newValue)
 * @method bool         updatePostExcerpt(int $id, string|Operand $newValue)
 * @method bool         updatePostStatus(int $id, string|Operand $newValue)
 * @method bool         updateCommentStatus(int $id, string|Operand $newValue)
 * @method bool         updatePingStatus(int $id, string|Operand $newValue)
 * @method bool         updatePostPassword(int $id, string|Operand $newValue)
 * @method bool         updatePostName(int $id, string|Operand $newValue)
 * @method bool         updateToPing(int $id, string|Operand $newValue)
 * @method bool         updatePinged(int $id, string|Operand $newValue)
 * @method bool         updatePostModifiedGmt(int $id, DateTimeInterface|Operand $newValue)
 * @method bool         updatePostParent(int $id, int|Operand $newValue)
 * @method bool         updateGuid(int $id, string|Operand $newValue)
 * @method bool         updateMenuOrder(int $id, int|Operand $newValue)
 * @method bool         updatePostType(int $id, string|Operand $newValue)
 * @method bool         updatePostMimeType(int $id, string|Operand $newValue)
 * @method bool         updateCommentCount(int $id, int|Operand $newValue)
 */
abstract class AbstractEntityRepository implements EntityRepositoryInterface
{
    protected const MAPPED_FIELDS = [];
    protected EntityManagerInterface $entityManager;
    protected SerializerInterface $serializer;

    private array $entityBaseFields = [];
    private array $entityExtraFields = [];
    private PropertyNormalizer $propertyNormalizer;

    public function __construct(
        private string $entityClassName,
    ) {
        $this->propertyNormalizer = new PropertyNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
    }

    public function __call(string $name, array $arguments): BaseEntity|array|bool
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

        throw new MethodNotFoundException(self::class, $name);
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function find(int $id): BaseEntity
    {
        return $this->findOneBy([
            'id' => $id,
        ]);
    }

    public function findOneBy(array $criteria, array $orderBy = null): BaseEntity
    {
        $result = $this->createFindByQueryBuilder($criteria, $orderBy)
            ->setMaxResults(1)
            ->setFirstResult(0)
            ->executeQuery()
            ->fetchAssociative()
        ;

        if ($result === false) {
            throw new EntityNotFoundException($this->entityClassName, $criteria);
        }

        return $this->denormalize($result, $this->entityClassName);
    }

    public function findAll(array $orderBy = null): array
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null): array
    {
        $result = $this->createFindByQueryBuilder($criteria, $orderBy)
            ->setMaxResults($limit)
            ->setFirstResult($offset ?? 0)
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        return $this->denormalize($result, $this->entityClassName . '[]');
    }

    public function updateSingleField(int $id, string $field, mixed $newValue): bool
    {
        $value = $this->normalize($field, $newValue);

        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->update($this->entityManager->getTablesPrefix() . 'posts')
            ->set($field, ':value')
            ->where('ID = :id')
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
            ->from($this->entityManager->getTablesPrefix() . 'posts', 'p')
            ->where('post_type = :post_type')
            ->setParameter('post_type', $this->getPostType())
        ;

        $extraFields = $this->getEntityExtraFields();

        if (!empty($extraFields)) {
            $queryBuilder->leftJoin(
                'p',
                $this->entityManager->getTablesPrefix() . 'postmeta',
                'pm_self',
                'p.ID = pm_self.post_id',
            );

            foreach ($extraFields as $extraField) {
                $fieldName = property_to_field($extraField);
                $mappedMetaKey = $this->getMappedMetaKey($fieldName);
                $queryBuilder->addSelect(select_from_eav($fieldName, $mappedMetaKey));
            }

            $queryBuilder->groupBy('p.ID');
        }

        foreach ($normalizedCriteria as $field => $value) {
            if ($value instanceof NestedCondition) {
                $this->createNestedCriteria($queryBuilder, $criteria[$field]->getCriteria(), $value);

                continue;
            }

            if ($value instanceof RelationshipCondition) {
                $this->createRelationshipCriteria($queryBuilder, $value);

                continue;
            }

            $expr = sprintf(
                '`%s` %s :%s',
                $field,
                $criteria[$field] instanceof Operand ? $criteria[$field]->getOperator() : '=',
                $field,
            );

            if (in_array($field, $extraFields, true)) {
                $queryBuilder->andHaving($expr);
            } else {
                $queryBuilder->andWhere($expr);
            }

            $queryBuilder->setParameter(
                $field,
                $criteria[$field] instanceof Operand ? $criteria[$field]->getOperand() : $value
            );
        }

        if (!empty($orderBy)) {
            foreach ($orderBy as $field => $orientation) {
                $this->validateFieldName($field);

                if (!in_array(strtolower($orientation), ['asc', 'desc'], true)) {
                    throw new InvalidOrderByOrientationException($orientation);
                }

                $queryBuilder->addOrderBy($field, $orientation);
            }
        }

        return $queryBuilder;
    }

    public function denormalize(mixed $data, string $type): mixed
    {
        $context[AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT] = true;

        return $this->serializer->denormalize($data, $type, null, $context);
    }

    /**
     * @return string[]
     */
    protected function getEntityBaseFields(): array
    {
        if (empty($this->entityBaseFields)) {
            $this->entityBaseFields = array_keys($this->serializer->normalize(new $this->entityClassName(), null, [
                'groups' => ['base'],
            ]));
        }

        return $this->entityBaseFields;
    }

    /**
     * @return string[]
     */
    protected function getPrefixedEntityBaseFields(string $prefix): array
    {
        return array_map(
            static fn (string $property): string => sprintf('%s.%s', $prefix, $property),
            $this->getEntityBaseFields(),
        );
    }

    /**
     * @return string[]
     */
    protected function getEntityExtraFields(): array
    {
        $baseFields = $this->getEntityBaseFields();

        if (empty($this->entityExtraFields)) {
            $allFields = array_keys($this->propertyNormalizer->normalize(new $this->entityClassName()));
            $this->entityExtraFields = array_diff($allFields, $baseFields);
        }

        return $this->entityExtraFields;
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

    private function doFindOneBy(string $name, array $arguments): BaseEntity
    {
        $resolver = (new OptionsResolver())
            ->setRequired(['0'])
            ->setDefault('1', [])
            ->setAllowedTypes('1', 'array')
        ;

        $fieldName = property_to_field(substr($name, 9));

        $arguments = $this->validateArguments($resolver, $arguments);
        $this->validateFieldValue($fieldName, $arguments[0]);

        return $this->findOneBy([
            $fieldName => $arguments[0],
        ], $arguments[1]);
    }

    private function doFindBy(string $name, array $arguments): array
    {
        $resolver = (new OptionsResolver())
            ->setRequired(['0'])
            ->setDefault('1', [])
            ->setDefault('2', null)
            ->setDefault('3', 0)
            ->setAllowedTypes('1', 'array')
            ->setAllowedTypes('2', ['int', 'null'])
            ->setAllowedTypes('3', 'int')
        ;

        $fieldName = property_to_field(substr($name, 6));

        $arguments = $this->validateArguments($resolver, $arguments);
        $this->validateFieldValue($fieldName, $arguments[0]);

        return $this->findBy([
            $fieldName => $arguments[0],
        ], ...array_slice($arguments, 1));
    }

    private function doUpdate(string $name, array $arguments): bool
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

    private function validateArguments(OptionsResolver $resolver, array $arguments): array
    {
        try {
            $arguments = $resolver->resolve($arguments);
        } catch (MissingOptionsException) {
            throw new InvalidArgumentException('Some arguments are missing.');
        } catch (InvalidOptionsException) {
            throw new InvalidArgumentException('Arguments provided are of the wrong type.');
        }

        ksort($arguments);

        return $arguments;
    }

    private function validateFieldName(string $fieldName): string
    {
        $propertyName = field_to_property($fieldName);

        try {
            $expectedType = (new \ReflectionProperty(BaseEntity::class, $propertyName))->getType();
        } catch (\ReflectionException) {
            try {
                $expectedType = (new \ReflectionProperty($this->entityClassName, $propertyName))->getType();
            } catch (\ReflectionException) {
                throw new InvalidFieldNameException(
                    $this->entityManager->getTablesPrefix() . 'posts',
                    strtolower($fieldName)
                );
            }
        }

        return $expectedType->getName();
    }

    private function validateFieldValue(string $field, mixed $value): mixed
    {
        $expectedType = $this->validateFieldName($field);
        $resolvedValue = $value instanceof Operand ? $value->getOperand() : $value;

        if ($value instanceof Operand && $value->isLooseOperator()) {
            return $resolvedValue;
        }

        $newValueType = str_replace(
            ['integer', 'boolean', 'double'],
            ['int', 'bool', 'float'],
            gettype($resolvedValue),
        );

        if (
            (is_object($resolvedValue) && !is_subclass_of($resolvedValue, $expectedType))
            || (!is_object($resolvedValue) && $expectedType !== $newValueType)
        ) {
            throw new InvalidTypeException(strtolower($field), $expectedType, $newValueType);
        }

        if (is_array($resolvedValue)) {
            $resolvedValue = serialize($resolvedValue);
        }

        return $resolvedValue;
    }

    private function normalizeCriteria(array $criteria): array
    {
        $output = [];

        foreach ($criteria as $field => $value) {
            if ($value instanceof NestedCondition) {
                $output[] = new NestedCondition($value->getOperator(), $this->normalizeCriteria($value->getCriteria()));
            } elseif ($value instanceof RelationshipCondition) {
                $output[] = $value;
            } else {
                $value = $this->validateFieldValue($field, $value);
                $output[$field] = (string) $this->serializer->normalize($value);
            }
        }

        return $output;
    }

    private function getMappedMetaKey(mixed $fieldName): string
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
}
