<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Williarin\WordpressInterop\Bridge\Type\AttachmentMetadata;

final class Attachment extends BaseEntity
{
    #[Groups(['attachment'])]
    public ?string $attachedFile = null;

    #[Groups(['attachment'])]
    public ?AttachmentMetadata $attachmentMetadata = null;
}
