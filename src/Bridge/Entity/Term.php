<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use Williarin\WordpressInterop\Attributes\Id;
use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\TermRepository;

#[RepositoryClass(TermRepository::class)]
final class Term
{
    #[Id]
    public ?int $termId = null;
    public ?string $taxonomy = null;
    public ?string $name = null;
    public ?string $slug = null;
    public ?int $count = null;
}
