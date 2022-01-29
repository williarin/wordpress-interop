<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\EntityManagerInterface;

/**
 * @method Post   find($id)
 * @method Post   findOneBy(array $criteria, array $orderBy = null)
 * @method Post   findOneByPostAuthor(int $newValue, array $orderBy = null)
 * @method Post   findOneByPostDate(DateTimeInterface $newValue, array $orderBy = null)
 * @method Post   findOneByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method Post   findOneByPostContent(string $newValue, array $orderBy = null)
 * @method Post   findOneByPostTitle(string $newValue, array $orderBy = null)
 * @method Post   findOneByPostExcerpt(string $newValue, array $orderBy = null)
 * @method Post   findOneByPostStatus(string $newValue, array $orderBy = null)
 * @method Post   findOneByCommentStatus(string $newValue, array $orderBy = null)
 * @method Post   findOneByPingStatus(string $newValue, array $orderBy = null)
 * @method Post   findOneByPostPassword(string $newValue, array $orderBy = null)
 * @method Post   findOneByPostName(string $newValue, array $orderBy = null)
 * @method Post   findOneByToPing(string $newValue, array $orderBy = null)
 * @method Post   findOneByPinged(string $newValue, array $orderBy = null)
 * @method Post   findOneByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method Post   findOneByPostParent(int $newValue, array $orderBy = null)
 * @method Post   findOneByGuid(string $newValue, array $orderBy = null)
 * @method Post   findOneByMenuOrder(int $newValue, array $orderBy = null)
 * @method Post   findOneByPostType(string $newValue, array $orderBy = null)
 * @method Post   findOneByPostMimeType(string $newValue, array $orderBy = null)
 * @method Post   findOneByCommentCount(int $newValue, array $orderBy = null)
 * @method Post[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostAuthor(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostDate(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostContent(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostTitle(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostExcerpt(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByCommentStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPingStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostPassword(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostName(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByToPing(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPinged(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostParent(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByGuid(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByMenuOrder(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostMimeType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByCommentCount(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findAll()
 */
final class PostRepository extends AbstractEntityRepository
{
    protected const POST_TYPE = 'post';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        parent::__construct($entityManager, $serializer, Post::class);
    }
}
