<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;

class EntityRepository implements RepositoryInterface
{
    protected const POST_TYPE = 'post';

    private array $entityProperties = [];

    public function __construct(
        protected EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private string $entityClassName
    ) {
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function find(int $id): mixed
    {
        $result = $this->entityManager->getConnection()->createQueryBuilder()
            ->select($this->getEntityProperties())
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

        return $this->serializer->denormalize($result, $this->entityClassName);
    }

    public function findAll(): mixed
    {
        $result = $this->entityManager->getConnection()->createQueryBuilder()
            ->select($this->getEntityProperties())
            ->from($this->entityManager->getTablesPrefix() . 'posts')
            ->where('post_type = :post_type')
            ->setParameters([
                'post_type' => static::POST_TYPE,
            ])
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        return $this->serializer->denormalize($result, $this->entityClassName . '[]');
    }

    private function getEntityProperties(): array
    {
        if (empty($this->entityProperties)) {
            $this->entityProperties = array_keys($this->serializer->normalize(new $this->entityClassName));
        }

        return $this->entityProperties;
    }

    private function getPrefixedEntityProperties(string $prefix): array
    {
        return array_map(
            static fn (string $property) => sprintf('%s.%s', $prefix, $property),
            $this->getEntityProperties(),
        );
    }
}
