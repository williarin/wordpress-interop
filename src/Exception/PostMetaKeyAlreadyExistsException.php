<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;

final class PostMetaKeyAlreadyExistsException extends Exception
{
    public function __construct(int $postId, string $metaKey)
    {
        parent::__construct(sprintf('Meta key "%s" already exists for post ID "%d".', $metaKey, $postId));
    }
}
