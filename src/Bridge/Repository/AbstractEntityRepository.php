<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;

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
        if (is_array($newValue)) {
            $newValue = serialize($newValue);
        }

        if (!in_array(strtolower($field), $this->getEntityBaseProperties(), true)) {
            throw new InvalidFieldNameException($this->entityManager->getTablesPrefix() . 'posts', strtolower($field));
        }

        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->update($this->entityManager->getTablesPrefix() . 'posts')
            ->set($field, ':value')
            ->where('ID = :id')
            ->setParameters([
                'id' => $id,
                'key' => strtolower($field),
                'value' => (string) $newValue,
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
}
