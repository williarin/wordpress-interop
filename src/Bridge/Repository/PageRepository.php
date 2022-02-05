<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Williarin\WordpressInterop\Bridge\Entity\Page;

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
 * @method Page[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostAuthor(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostDate(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostContent(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostTitle(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostExcerpt(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByCommentStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPingStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostPassword(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostName(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByToPing(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPinged(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostParent(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByGuid(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByMenuOrder(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostMimeType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByCommentCount(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findAll()
 */
final class PageRepository extends AbstractEntityRepository
{
    public function __construct()
    {
        parent::__construct(Page::class);
    }

    protected function getPostType(): string
    {
        return 'page';
    }
}
