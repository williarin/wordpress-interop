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
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
use Williarin\WordpressInterop\Exception\InvalidOrderByOrientationException;
use Williarin\WordpressInterop\Exception\InvalidTypeException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
use function Williarin\WordpressInterop\Util\String\field_to_property;
use function Williarin\WordpressInterop\Util\String\property_to_field;

/**
 * @method BaseEntity   findOneByPostAuthor(int $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostDate(DateTimeInterface $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostContent(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostTitle(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostExcerpt(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostStatus(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByCommentStatus(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPingStatus(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostPassword(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostName(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByToPing(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPinged(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostParent(int $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByGuid(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByMenuOrder(int $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostType(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByPostMimeType(string $newValue, array $orderBy = null)
 * @method BaseEntity   findOneByCommentCount(int $newValue, array $orderBy = null)
 * @method BaseEntity[] findByPostAuthor(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostDate(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostContent(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostTitle(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostExcerpt(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByCommentStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPingStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostPassword(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostName(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByToPing(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPinged(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostParent(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByGuid(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByMenuOrder(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByPostMimeType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method BaseEntity[] findByCommentCount(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method bool         updatePostAuthor(int $id, int $newValue)
 * @method bool         updatePostDate(int $id, DateTimeInterface $newValue)
 * @method bool         updatePostDateGmt(int $id, DateTimeInterface $newValue)
 * @method bool         updatePostContent(int $id, string $newValue)
 * @method bool         updatePostTitle(int $id, string $newValue)
 * @method bool         updatePostExcerpt(int $id, string $newValue)
 * @method bool         updatePostStatus(int $id, string $newValue)
 * @method bool         updateCommentStatus(int $id, string $newValue)
 * @method bool         updatePingStatus(int $id, string $newValue)
 * @method bool         updatePostPassword(int $id, string $newValue)
 * @method bool         updatePostName(int $id, string $newValue)
 * @method bool         updateToPing(int $id, string $newValue)
 * @method bool         updatePinged(int $id, string $newValue)
 * @method bool         updatePostModifiedGmt(int $id, DateTimeInterface $newValue)
 * @method bool         updatePostParent(int $id, int $newValue)
 * @method bool         updateGuid(int $id, string $newValue)
 * @method bool         updateMenuOrder(int $id, int $newValue)
 * @method bool         updatePostType(int $id, string $newValue)
 * @method bool         updatePostMimeType(int $id, string $newValue)
 * @method bool         updateCommentCount(int $id, int $newValue)
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
            ->executeQuery()
            ->fetchAssociative()
        ;

        if ($result === false) {
            throw new EntityNotFoundException($this->entityClassName, $criteria);
        }

        return $this->denormalize($result, $this->entityClassName);
    }

    public function findAll(): array
    {
        $result = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select($this->getEntityBaseFields())
            ->from($this->entityManager->getTablesPrefix() . 'posts')
            ->where('post_type = :post_type')
            ->setParameters([
                'post_type' => $this->getPostType(),
            ])
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        return $this->denormalize($result, $this->entityClassName . '[]');
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

    protected function denormalize(mixed $data, string $type): mixed
    {
        $context[AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT] = true;

        return $this->serializer->denormalize($data, $type, null, $context);
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
        $newValueType = str_replace(['integer', 'boolean', 'double'], ['int', 'bool', 'float'], gettype($value));

        if (
            (is_object($value) && !is_subclass_of($value, $expectedType))
            || (!is_object($value) && $expectedType !== $newValueType)
        ) {
            throw new InvalidTypeException(strtolower($field), $expectedType, $newValueType);
        }

        if (is_array($value)) {
            $value = serialize($value);
        }

        return $value;
    }

    private function normalizeCriteria(array $criteria): array
    {
        $output = [];

        foreach ($criteria as $field => $value) {
            $value = $this->validateFieldValue($field, $value);
            $output[$field] = (string) $this->serializer->normalize($value);
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

    private function createFindByQueryBuilder(array $criteria, ?array $orderBy): QueryBuilder
    {
        $criteria = $this->normalizeCriteria($criteria);

        $queryBuilder = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select($this->getPrefixedEntityBaseFields('p'))
            ->from($this->entityManager->getTablesPrefix() . 'posts', 'p')
            ->where('post_type = :post_type')
            ->setParameter('post_type', $this->getPostType())
        ;

        $extraFields = $this->getEntityExtraFields();

        if (!empty($extraFields)) {
            $queryBuilder->join(
                'p',
                $this->entityManager->getTablesPrefix() . 'postmeta',
                'pm_self',
                'p.ID = pm_self.post_id',
            );

            foreach ($extraFields as $extraField) {
                $fieldName = property_to_field($extraField);
                $mappedMetaKey = $this->getMappedMetaKey($fieldName);
                $queryBuilder->addSelect(
                    sprintf(
                        "MAX(Case WHEN pm_self.meta_key = '%s' THEN pm_self.meta_value END) `%s`",
                        $mappedMetaKey,
                        $fieldName,
                    )
                );
            }

            $queryBuilder->groupBy('p.ID');
        }

        foreach ($criteria as $field => $value) {
            if (in_array($field, $extraFields, true)) {
                $queryBuilder->andHaving("`{$field}` = :{$field}");
            } else {
                $queryBuilder->andWhere("`{$field}` = :{$field}");
            }

            $queryBuilder->setParameter($field, $value);
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
}
