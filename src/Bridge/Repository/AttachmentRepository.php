<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\Attachment;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Repository\EntityRepository;

/**
 * @method Attachment|null find($id)
 * @method Attachment[]    findAll()
 */
final class AttachmentRepository extends EntityRepository
{
    protected const POST_TYPE = 'attachment';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SerializerInterface $serializer
    ) {
        parent::__construct($entityManager, $serializer, Attachment::class);
    }

    public function getFeaturedImage(int $postId): Attachment
    {
        $result = $this->entityManager->getConnection()
            ->createQueryBuilder()
            ->select($this->getPrefixedEntityBaseProperties('p'))
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

        return $this->serializer->denormalize($result, Attachment::class);
    }
}
