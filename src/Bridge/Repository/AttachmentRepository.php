<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Williarin\WordpressInterop\Bridge\Entity\Attachment;
use Williarin\WordpressInterop\Criteria\Operand;

/**
 * @method Attachment   find($id)
 * @method Attachment   findOneBy(array $criteria, array $orderBy = null)
 * @method Attachment   findOneByPostAuthor(int|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostDate(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostDateGmt(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostContent(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostTitle(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostExcerpt(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostStatus(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByCommentStatus(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPingStatus(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostPassword(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostName(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByToPing(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPinged(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostModifiedGmt(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostParent(int|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByGuid(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByMenuOrder(int|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostType(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostMimeType(string|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByCommentCount(int|Operand $newValue, array $orderBy = null)
 * @method Attachment   findOneByAttachedFile(string|Operand $newValue, array $orderBy = null)
 * @method Attachment[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostAuthor(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostDate(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostDateGmt(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostContent(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostTitle(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostExcerpt(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByCommentStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPingStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostPassword(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostName(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByToPing(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPinged(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostModifiedGmt(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostParent(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByGuid(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByMenuOrder(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostMimeType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByCommentCount(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByAttachedFile(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findAll()
 */
final class AttachmentRepository extends AbstractEntityRepository
{
    protected const MAPPED_FIELDS = [
        '_wp_attached_file' => 'attached_file',
        '_wp_attachment_metadata' => 'attachment_metadata',
    ];

    public function __construct()
    {
        parent::__construct(Attachment::class);
    }

    public function getFeaturedImage(int $postId): Attachment
    {
        $result = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select($this->getPrefixedEntityBaseFields('p'))
            ->addSelect(
                "MAX(Case WHEN pm_self.meta_key = '_wp_attached_file' THEN pm_self.meta_value END) attached_file"
            )
            ->addSelect(
                "MAX(Case WHEN pm_self.meta_key = '_wp_attachment_metadata' THEN pm_self.meta_value END) attachment_metadata"
            )
            ->from($this->entityManager->getTablesPrefix() . 'posts', 'p')
            ->join(
                'p',
                $this->entityManager->getTablesPrefix() . 'postmeta',
                'pm_parent',
                'p.ID = pm_parent.meta_value'
            )
            ->join('p', $this->entityManager->getTablesPrefix() . 'postmeta', 'pm_self', 'p.ID = pm_self.post_id')
            ->where('pm_parent.post_id = :id')
            ->andWhere("pm_parent.meta_key = '_thumbnail_id'")
            ->andWhere("p.post_type = 'attachment'")
            ->groupBy('p.ID')
            ->setParameters([
                'id' => $postId,
            ])
            ->executeQuery()
            ->fetchAssociative()
        ;

        return $this->denormalize($result, Attachment::class);
    }

    protected function getPostType(): string
    {
        return 'attachment';
    }
}
