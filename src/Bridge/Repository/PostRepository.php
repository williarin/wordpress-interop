<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\Criteria\Operand;

/**
 * @method Post   find($id)
 * @method Post   findOneBy(array $criteria, array $orderBy = null)
 * @method Post   findOneByPostAuthor(int|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostDate(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostDateGmt(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostContent(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostTitle(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostExcerpt(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostStatus(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByCommentStatus(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPingStatus(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostPassword(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostName(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByToPing(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPinged(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostModifiedGmt(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostParent(int|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByGuid(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByMenuOrder(int|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostType(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByPostMimeType(string|Operand $newValue, array $orderBy = null)
 * @method Post   findOneByCommentCount(int|Operand $newValue, array $orderBy = null)
 * @method Post[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostAuthor(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostDate(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostDateGmt(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostContent(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostTitle(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostExcerpt(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByCommentStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPingStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostPassword(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostName(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByToPing(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPinged(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostModifiedGmt(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostParent(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByGuid(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByMenuOrder(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByPostMimeType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findByCommentCount(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Post[] findAll()
 */
final class PostRepository extends AbstractEntityRepository
{
    public function __construct()
    {
        parent::__construct(Post::class);
    }
}
