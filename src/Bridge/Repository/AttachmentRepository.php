<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Williarin\WordpressInterop\Bridge\Entity\Attachment;

/**
 * @method Attachment   find($id)
 * @method Attachment   findOneBy(array $criteria, array $orderBy = null)
 * @method Attachment   findOneByPostAuthor(int $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostDate(DateTimeInterface $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostContent(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostTitle(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostExcerpt(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostStatus(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByCommentStatus(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByPingStatus(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostPassword(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostName(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByToPing(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByPinged(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostParent(int $newValue, array $orderBy = null)
 * @method Attachment   findOneByGuid(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByMenuOrder(int $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostType(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByPostMimeType(string $newValue, array $orderBy = null)
 * @method Attachment   findOneByCommentCount(int $newValue, array $orderBy = null)
 * @method Attachment   findOneByAttachedFile(string $newValue, array $orderBy = null)
 * @method Attachment[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostAuthor(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostDate(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostContent(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostTitle(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostExcerpt(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByCommentStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPingStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostPassword(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostName(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByToPing(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPinged(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostParent(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByGuid(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByMenuOrder(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByPostMimeType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByCommentCount(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Attachment[] findByAttachedFile(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
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
