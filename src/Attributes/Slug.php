<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Attributes;

use Attribute;

/**
 * Mark the property as slug. When duplicating an entity, all slug properties will be slugged.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Slug
{
}
