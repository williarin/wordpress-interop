<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Type;

final class AttachmentMetadata
{
    public ?int $width = null;

    public ?int $height = null;

    public ?string $file = null;

    public ?array $sizes = null;

    public ?array $imageMeta = null;
}
