<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Williarin\WordpressInterop\Bridge\Entity\Page;
use Williarin\WordpressInterop\Criteria\Operand;

/**
 * @method Page   find($id)
 * @method Page   findOneBy(array $criteria, array $orderBy = null)
 * @method Page   findOneByPostAuthor(int|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostDate(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostDateGmt(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostContent(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostTitle(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostExcerpt(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostStatus(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByCommentStatus(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPingStatus(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostPassword(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostName(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByToPing(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPinged(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostModifiedGmt(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostParent(int|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByGuid(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByMenuOrder(int|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostType(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByPostMimeType(string|Operand $newValue, array $orderBy = null)
 * @method Page   findOneByCommentCount(int|Operand $newValue, array $orderBy = null)
 * @method Page[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostAuthor(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostDate(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostDateGmt(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostContent(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostTitle(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostExcerpt(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByCommentStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPingStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostPassword(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostName(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByToPing(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPinged(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostModifiedGmt(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostParent(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByGuid(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByMenuOrder(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByPostMimeType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findByCommentCount(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Page[] findAll(array $orderBy = null)
 */
class PageRepository extends AbstractEntityRepository
{
    /** @deprecated Left for BC reasons only, use getMappedFields instead */
    protected const MAPPED_FIELDS = ['thumbnail_id'];

    public function __construct()
    {
        parent::__construct(Page::class);
    }

    protected function getPostType(): string
    {
        return 'page';
    }

    protected function getMappedFields(): array
    {
        return self::MAPPED_FIELDS;
    }
}
