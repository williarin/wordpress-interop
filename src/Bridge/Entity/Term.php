<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use AllowDynamicProperties;
use Symfony\Component\Serializer\Annotation\Groups;
use Williarin\WordpressInterop\Attributes\External;
use Williarin\WordpressInterop\Attributes\Id;
use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\TermRepository;

#[AllowDynamicProperties]
#[RepositoryClass(TermRepository::class)]
class Term
{
    use DynamicPropertiesTrait;

    #[Id]
    #[Groups('base')]
    public ?int $termId = null;

    #[Groups('base')]
    public ?string $name = null;

    #[Groups('base')]
    public ?string $slug = null;

    #[External]
    public ?string $taxonomy = null;

    #[External]
    public ?int $termTaxonomyId = null;

    #[External]
    public ?int $count = null;
}
