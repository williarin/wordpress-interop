<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class RepositoryClass
{
    public function __construct(public string $className)
    {
    }
}
