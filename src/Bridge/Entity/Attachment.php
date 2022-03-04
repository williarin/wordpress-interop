<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\AttachmentRepository;
use Williarin\WordpressInterop\Bridge\Type\AttachmentMetadata;

#[RepositoryClass(AttachmentRepository::class)]
class Attachment extends BaseEntity
{
    public ?string $attachedFile = null;
    public ?AttachmentMetadata $attachmentMetadata = null;
}
