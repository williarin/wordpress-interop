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
use Williarin\WordpressInterop\Exception\InvalidTypeException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
use function Symfony\Component\String\u;

/**
 * @method bool updatePostAuthor(int $id, int $newValue)
 * @method bool updatePostDate(int $id, DateTimeInterface $newValue)
 * @method bool updatePostDateGmt(int $id, DateTimeInterface $newValue)
 * @method bool updatePostContent(int $id, string $newValue)
 * @method bool updatePostTitle(int $id, string $newValue)
 * @method bool updatePostExcerpt(int $id, string $newValue)
 * @method bool updatePostStatus(int $id, string $newValue)
 * @method bool updateCommentStatus(int $id, string $newValue)
 * @method bool updatePingStatus(int $id, string $newValue)
 * @method bool updatePostPassword(int $id, string $newValue)
 * @method bool updatePostName(int $id, string $newValue)
 * @method bool updateToPing(int $id, string $newValue)
 * @method bool updatePinged(int $id, string $newValue)
 * @method bool updatePostModifiedGmt(int $id, DateTimeInterface $newValue)
 * @method bool updatePostParent(int $id, int $newValue)
 * @method bool updateGuid(int $id, string $newValue)
 * @method bool updateMenuOrder(int $id, int $newValue)
 * @method bool updatePostType(int $id, string $newValue)
 * @method bool updatePostMimeType(int $id, string $newValue)
 * @method bool updateCommentCount(int $id, int $newValue)
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

    public function __call(string $name, array $arguments): string|array|bool|null
    {
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
        $result = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select($this->getEntityBaseProperties())
            ->from($this->entityManager->getTablesPrefix() . 'posts')
            ->where('ID = :id')
            ->andWhere('post_type = :post_type')
            ->setParameters([
                'id' => $id,
                'post_type' => static::POST_TYPE,
            ])
            ->executeQuery()
            ->fetchAssociative()
        ;

        if ($result === false) {
            throw new EntityNotFoundException($this->entityClassName, $id);
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
        $propertyName = u($field)
            ->lower()
            ->camel()
            ->toString()
        ;
        $newValueType = str_replace(['integer', 'boolean', 'double'], ['int', 'bool', 'float'], gettype($newValue));

        try {
            $expectedType = (new \ReflectionProperty(BaseEntity::class, $propertyName))->getType();
        } catch (\ReflectionException) {
            throw new InvalidFieldNameException($this->entityManager->getTablesPrefix() . 'posts', strtolower($field));
        }

        if (
            (is_object($newValue) && !is_subclass_of($newValue, $expectedType->getName()))
            || (!is_object($newValue) && $expectedType->getName() !== $newValueType)
        ) {
            throw new InvalidTypeException(strtolower($field), $expectedType->getName(), $newValueType);
        }

        if (is_array($newValue)) {
            $newValue = serialize($newValue);
        }

        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->update($this->entityManager->getTablesPrefix() . 'posts')
            ->set($field, ':value')
            ->where('ID = :id')
            ->setParameters([
                'id' => $id,
                'key' => strtolower($field),
                'value' => (string) $this->serializer->normalize($newValue),
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

    private function getEntityBaseProperties(): array
    {
        if (empty($this->entityProperties)) {
            $this->entityProperties = array_keys($this->serializer->normalize(new $this->entityClassName(), null, [
                'groups' => ['base'],
            ]));
        }

        return $this->entityProperties;
    }

    private function doUpdate(string $name, array $arguments): bool
    {
        $resolver = (new OptionsResolver())
            ->setRequired(['0', '1'])
            ->setAllowedTypes('0', 'int')
            ->setInfo('0', 'The ID of the entity to update.')
            ->setInfo('1', 'The new value.')
        ;

        try {
            $arguments = $resolver->resolve($arguments);
        } catch (MissingOptionsException) {
            throw new InvalidArgumentException(
                'This method requires two arguments: updatePostContent(int $id, mixed $newValue).'
            );
        } catch (InvalidOptionsException) {
            throw new InvalidArgumentException(sprintf(
                'The option "$id" with value "%s" is expected to be of type "int", but is of type "%s".',
                $arguments[0],
                gettype($arguments[0]),
            ));
        }

        $fieldName = u(substr($name, 6))
            ->snake()
            ->lower()
            ->toString()
        ;

        return $this->updateSingleField($arguments[0], $fieldName, $arguments[1]);
    }
}
