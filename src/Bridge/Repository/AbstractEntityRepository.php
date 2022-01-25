<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
use Williarin\WordpressInterop\Exception\InvalidOrderByOrientationException;
use Williarin\WordpressInterop\Exception\InvalidTypeException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
use function Symfony\Component\String\u;

/**
 * @method BaseEntity findOneByPostAuthor(int $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostDate(DateTimeInterface $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostContent(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostTitle(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostExcerpt(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostStatus(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByCommentStatus(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPingStatus(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostPassword(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostName(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByToPing(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPinged(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostParent(int $newValue, array $orderBy = null)
 * @method BaseEntity findOneByGuid(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByMenuOrder(int $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostType(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByPostMimeType(string $newValue, array $orderBy = null)
 * @method BaseEntity findOneByCommentCount(int $newValue, array $orderBy = null)
 * @method bool       updatePostAuthor(int $id, int $newValue)
 * @method bool       updatePostDate(int $id, DateTimeInterface $newValue)
 * @method bool       updatePostDateGmt(int $id, DateTimeInterface $newValue)
 * @method bool       updatePostContent(int $id, string $newValue)
 * @method bool       updatePostTitle(int $id, string $newValue)
 * @method bool       updatePostExcerpt(int $id, string $newValue)
 * @method bool       updatePostStatus(int $id, string $newValue)
 * @method bool       updateCommentStatus(int $id, string $newValue)
 * @method bool       updatePingStatus(int $id, string $newValue)
 * @method bool       updatePostPassword(int $id, string $newValue)
 * @method bool       updatePostName(int $id, string $newValue)
 * @method bool       updateToPing(int $id, string $newValue)
 * @method bool       updatePinged(int $id, string $newValue)
 * @method bool       updatePostModifiedGmt(int $id, DateTimeInterface $newValue)
 * @method bool       updatePostParent(int $id, int $newValue)
 * @method bool       updateGuid(int $id, string $newValue)
 * @method bool       updateMenuOrder(int $id, int $newValue)
 * @method bool       updatePostType(int $id, string $newValue)
 * @method bool       updatePostMimeType(int $id, string $newValue)
 * @method bool       updateCommentCount(int $id, int $newValue)
 */
abstract class AbstractEntityRepository implements RepositoryInterface
{
    protected const POST_TYPE = 'post';

    private array $entityProperties = [];

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SerializerInterface $serializer,
        private string $entityClassName
    ) {
    }

    public function __call(string $name, array $arguments): BaseEntity|bool
    {
        if (str_starts_with($name, 'findOneBy')) {
            return $this->doFindOneBy($name, $arguments);
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
        $criteria = $this->normalizeCriteria($criteria);

        $queryBuilder = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select($this->getEntityBaseProperties())
            ->from($this->entityManager->getTablesPrefix() . 'posts')
            ->where('post_type = :post_type')
            ->setParameter('post_type', static::POST_TYPE)
        ;

        foreach ($criteria as $field => $value) {
            $queryBuilder->andWhere("{$field} = :{$field}")
                ->setParameter($field, $value)
            ;
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

        $result = $queryBuilder->executeQuery()
            ->fetchAssociative()
        ;

        if ($result === false) {
            throw new EntityNotFoundException($this->entityClassName, $criteria);
        }

        return $this->denormalize($result, $this->entityClassName);
    }

    public function findAll(): mixed
    {
        $result = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select($this->getEntityBaseProperties())
            ->from($this->entityManager->getTablesPrefix() . 'posts')
            ->where('post_type = :post_type')
            ->setParameters([
                'post_type' => static::POST_TYPE,
            ])
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

    /**
     * @return string[]
     */
    protected function getPrefixedEntityBaseProperties(string $prefix): array
    {
        return array_map(
            static fn (string $property): string => sprintf('%s.%s', $prefix, $property),
            $this->getEntityBaseProperties(),
        );
    }

    protected function denormalize(mixed $data, string $type): mixed
    {
        $context = [];

        if (PHP_VERSION_ID < 80100) {
            $context[AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT] = true;
        }

        return $this->serializer->denormalize($data, $type, null, $context);
    }

    protected function normalize(string $field, mixed $value): string
    {
        $value = $this->validateFieldValue($field, $value);

        return (string) $this->serializer->normalize($value);
    }

    private function getEntityBaseProperties(): array
    {
        if (empty($this->entityProperties)) {
            $this->entityProperties = array_keys($this->serializer->normalize(new $this->entityClassName(), null, [
                'groups' => ['base'],
            ]));
        }

        return $this->entityProperties;
    }

    private function doFindOneBy(string $name, array $arguments): BaseEntity
    {
        $resolver = (new OptionsResolver())
            ->setRequired(['0'])
            ->setDefault('1', [])
            ->setAllowedTypes('1', 'array')
        ;

        $fieldName = u(substr($name, 9))
            ->snake()
            ->lower()
            ->toString()
        ;

        $arguments = $this->validateArguments($resolver, $arguments);
        $this->validateFieldValue($fieldName, $arguments[0]);

        return $this->findOneBy([
            $fieldName => $arguments[0],
        ], $arguments[1]);
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

        $fieldName = u(substr($name, 6))
            ->snake()
            ->lower()
            ->toString()
        ;

        return $this->updateSingleField($arguments[0], $fieldName, $arguments[1]);
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

        return $arguments;
    }

    private function validateFieldName(string $field): string
    {
        $propertyName = u($field)
            ->lower()
            ->camel()
            ->toString()
        ;

        try {
            $expectedType = (new \ReflectionProperty(BaseEntity::class, $propertyName))->getType();
        } catch (\ReflectionException) {
            throw new InvalidFieldNameException($this->entityManager->getTablesPrefix() . 'posts', strtolower($field));
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
}
