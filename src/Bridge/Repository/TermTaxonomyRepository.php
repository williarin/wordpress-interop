<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\TermTaxonomy;

class TermTaxonomyRepository extends AbstractEntityRepository
{
    protected const TABLE_NAME = 'term_taxonomy';
    protected const TABLE_IDENTIFIER = 'term_taxonomy_id';
    protected const FALLBACK_ENTITY = TermTaxonomy::class;

    public function __construct()
    {
        parent::__construct(TermTaxonomy::class);
    }
}
