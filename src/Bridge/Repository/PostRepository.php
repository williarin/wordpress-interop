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
