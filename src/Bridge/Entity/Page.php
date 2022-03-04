<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\PageRepository;

#[RepositoryClass(PageRepository::class)]
class Page extends BaseEntity
{
    public ?int $thumbnailId = null;
}
