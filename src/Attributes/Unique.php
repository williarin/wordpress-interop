<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Attributes;

use Attribute;

/**
 * Mark the property as unique. When duplicating an entity, all unique properties will have a suffix appended.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Unique
{
}
