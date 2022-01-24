<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\PostMeta;
use Williarin\WordpressInterop\EntityManagerInterface;
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

    public function findValueByKey(int $postId, string $metaKey, bool $unserialize = true): string|array|null
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
            return null;
        }

        return $unserialize ? unserialize_if_needed($result) : $result;
    }
}
