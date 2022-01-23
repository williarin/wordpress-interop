<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Util\String;

function unserialize_if_needed(string $data): string|array
{
    $unserialized = @unserialize($data, ['allowed_classes' => []]);

    if ($data === 'b:0;' || $unserialized !== false) {
        return $unserialized;
    }

    return $data;
}
