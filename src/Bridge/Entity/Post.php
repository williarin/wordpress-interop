<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\PostRepository;

#[RepositoryClass(PostRepository::class)]
final class Post extends BaseEntity
{
}
