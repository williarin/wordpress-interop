<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
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
    use NestedCriteriaTrait;
    use FindByTrait;

    protected const MAPPED_FIELDS = [];
    protected const TABLE_NAME = 'posts';

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

    public function updateSingleField(int $id, string $field, mixed $newValue): bool
    {
        $value = $this->normalize($field, $newValue);

        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->update($this->entityManager->getTablesPrefix() . self::TABLE_NAME)
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
            ->from($this->entityManager->getTablesPrefix() . self::TABLE_NAME, 'p')
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
            if ($value instanceof SelectColumns) {
                $this->selectColumns($queryBuilder, $extraFields, $value);

                continue;
            }

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
                $this->validateFieldName($field, BaseEntity::class, self::TABLE_NAME);

                if (!in_array(strtolower($orientation), ['asc', 'desc'], true)) {
                    throw new InvalidOrderByOrientationException($orientation);
                }

                $queryBuilder->addOrderBy($field, $orientation);
            }
        }

        return $queryBuilder;
    }

    /**
     * @return string[]
     */
    protected function getEntityBaseFields(): array
    {
        if (empty($this->entityBaseFields)) {
            $entityClassName = $this->getEntityClassName();
            $this->entityBaseFields = array_keys($this->serializer->normalize(new $entityClassName(), null, [
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
            $entityClassName = $this->getEntityClassName();
            $allFields = array_keys($this->propertyNormalizer->normalize(new $entityClassName()));
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
