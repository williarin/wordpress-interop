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
    return u(($portion = strstr($fieldName, '.')) ? substr($portion, 1) : $fieldName)
        ->lower()
        ->camel()
        ->toString()
    ;
}

function select_from_eav(string $fieldName, ?string $metaKey = null, string $joinTableName = 'pm_self'): string
{
    $fieldName = property_to_field($fieldName);

    return sprintf(
        "MAX(CASE WHEN %s.meta_key = '%s' THEN %s.meta_value END) AS `%s`",
        $joinTableName,
        $metaKey ?? sprintf('_%s', ltrim($fieldName, '_')),
        $joinTableName,
        $fieldName,
    );
}
