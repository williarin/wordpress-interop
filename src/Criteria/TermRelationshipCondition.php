<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Criteria;

final class TermRelationshipCondition
{
    public function __construct(
        private array $criteria,
    ) {
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }
}
