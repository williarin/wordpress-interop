<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\Page;
use Williarin\WordpressInterop\EntityManagerInterface;

/**
 * @method Page   find($id)
 * @method Page   findOneBy(array $criteria, array $orderBy = null)
 * @method Page   findOneByPostAuthor(int $newValue, array $orderBy = null)
 * @method Page   findOneByPostDate(DateTimeInterface $newValue, array $orderBy = null)
 * @method Page   findOneByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method Page   findOneByPostContent(string $newValue, array $orderBy = null)
 * @method Page   findOneByPostTitle(string $newValue, array $orderBy = null)
 * @method Page   findOneByPostExcerpt(string $newValue, array $orderBy = null)
 * @method Page   findOneByPostStatus(string $newValue, array $orderBy = null)
 * @method Page   findOneByCommentStatus(string $newValue, array $orderBy = null)
 * @method Page   findOneByPingStatus(string $newValue, array $orderBy = null)
 * @method Page   findOneByPostPassword(string $newValue, array $orderBy = null)
 * @method Page   findOneByPostName(string $newValue, array $orderBy = null)
 * @method Page   findOneByToPing(string $newValue, array $orderBy = null)
 * @method Page   findOneByPinged(string $newValue, array $orderBy = null)
 * @method Page   findOneByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method Page   findOneByPostParent(int $newValue, array $orderBy = null)
 * @method Page   findOneByGuid(string $newValue, array $orderBy = null)
 * @method Page   findOneByMenuOrder(int $newValue, array $orderBy = null)
 * @method Page   findOneByPostType(string $newValue, array $orderBy = null)
 * @method Page   findOneByPostMimeType(string $newValue, array $orderBy = null)
 * @method Page   findOneByCommentCount(int $newValue, array $orderBy = null)
 * @method Page[] findAll()
 */
final class PageRepository extends AbstractEntityRepository
{
    protected const POST_TYPE = 'page';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        parent::__construct($entityManager, $serializer, Page::class);
    }
}
