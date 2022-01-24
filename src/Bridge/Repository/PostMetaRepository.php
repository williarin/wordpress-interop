<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\PostMeta;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\PostMetaKeyAlreadyExistsException;
use Williarin\WordpressInterop\Exception\PostMetaKeyNotFoundException;
use Williarin\WordpressInterop\Repository\RepositoryInterface;
use function Williarin\WordpressInterop\Util\String\unserialize_if_needed;

final class PostMetaRepository implements RepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
    }

    public function getEntityClassName(): string
    {
        return PostMeta::class;
    }

    public function find(int $postId, string $metaKey, bool $unserialize = true): string|array|int|bool|null
    {
        /** @var string|false $result */
        $result = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select('meta_value')
            ->from($this->entityManager->getTablesPrefix() . 'postmeta')
            ->where('post_id = :id')
            ->andWhere('meta_key = :key')
            ->setParameters([
                'id' => $postId,
                'key' => $metaKey,
            ])
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne()
        ;

        if ($result === false) {
            throw new PostMetaKeyNotFoundException($postId, $metaKey);
        }

        return $unserialize ? unserialize_if_needed($result) : $result;
    }

    public function create(int $postId, string $metaKey, mixed $metaValue): bool
    {
        try {
            $this->find($postId, $metaKey);
            throw new PostMetaKeyAlreadyExistsException($postId, $metaKey);
        } catch (PostMetaKeyNotFoundException) {
        }

        if (is_array($metaValue)) {
            $metaValue = serialize($metaValue);
        }

        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->insert($this->entityManager->getTablesPrefix() . 'postmeta')
            ->values([
                'post_id' => ':id',
                'meta_key' => ':key',
                'meta_value' => ':value',
            ])
            ->setParameters([
                'id' => $postId,
                'key' => $metaKey,
                'value' => (string)$metaValue,
            ])
            ->executeStatement()
        ;

        return $affectedRows > 0;
    }

    public function update(int $postId, string $metaKey, mixed $metaValue): bool
    {
        if (is_array($metaValue)) {
            $metaValue = serialize($metaValue);
        }

        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->update($this->entityManager->getTablesPrefix() . 'postmeta')
            ->set('meta_value', ':value')
            ->where('post_id = :id')
            ->andWhere('meta_key = :key')
            ->setParameters([
                'id' => $postId,
                'key' => $metaKey,
                'value' => (string)$metaValue,
            ])
            ->executeStatement()
        ;

        return $affectedRows > 0;
    }

    public function delete(int $postId, string $metaKey): bool
    {
        $affectedRows = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->delete($this->entityManager->getTablesPrefix() . 'postmeta')
            ->where('post_id = :id')
            ->andWhere('meta_key = :key')
            ->setParameters([
                'id' => $postId,
                'key' => $metaKey,
            ])
            ->executeStatement()
        ;

        return $affectedRows > 0;
    }
}
