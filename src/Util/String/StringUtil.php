<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Util\String;

use function Symfony\Component\String\u;

function unserialize_if_needed(string $data): string|array
{
    $unserialized = @unserialize($data, [
        'allowed_classes' => [],
    ]);

    if ($data === 'b:0;' || $unserialized !== false) {
        return $unserialized;
    }

    return $data;
}

function property_to_field(string $propertyName): string
{
    return u($propertyName)
        ->snake()
        ->lower()
        ->toString()
    ;
}

function field_to_property(string $fieldName): string
{
    return u($fieldName)
        ->lower()
        ->camel()
        ->toString()
    ;
}

function select_from_eav(string $propertyName, ?string $metaKey = null, string $joinTableName = 'pm_self'): string
{
    return sprintf(
        "MAX(CASE WHEN %s.meta_key = '%s' THEN %s.meta_value END) `%s`",
        $joinTableName,
        $metaKey ?? sprintf('_%s', ltrim($propertyName, '_')),
        $joinTableName,
        $propertyName,
    );
}
