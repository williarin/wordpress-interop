<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Persistence;

use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\EntityManagerAwareInterface;

interface DuplicationServiceInterface extends EntityManagerAwareInterface
{
    public const POST_STATUS_DRAFT = 'draft';
    public const POST_STATUS_PRIVATE = 'private';
    public const POST_STATUS_PUBLISH = 'publish';

    public function duplicate(
        BaseEntity|int $entityOrId,
        ?string $entityType = null,
        string $postStatus = self::POST_STATUS_DRAFT,
        string $suffix = ' (Copy)',
    ): BaseEntity;
}
