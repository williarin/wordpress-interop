<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Fixture\Entity;

use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Test\Fixture\Repository\FooRepository;

#[RepositoryClass(FooRepository::class)]
final class Foo
{
}
