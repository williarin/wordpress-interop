<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use AllowDynamicProperties;
use Symfony\Component\Serializer\Annotation\Groups;
use Williarin\WordpressInterop\Attributes\Id;
use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\TermTaxonomyRepository;

#[AllowDynamicProperties]
#[RepositoryClass(TermTaxonomyRepository::class)]
class TermTaxonomy
{
    #[Id]
    #[Groups('base')]
    public ?int $termTaxonomyId = null;

    #[Groups('base')]
    public ?int $termId = null;

    #[Groups('base')]
    public ?string $taxonomy = null;

    #[Groups('base')]
    public ?string $description = null;

    #[Groups('base')]
    public int $parent = 0;

    #[Groups('base')]
    public int $count = 0;

    public function setTermId(?int $termId): self
    {
        $this->termId = $termId;
        return $this;
    }

    public function setTaxonomy(?string $taxonomy): self
    {
        $this->taxonomy = $taxonomy;
        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setParent(int $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;
        return $this;
    }
}
