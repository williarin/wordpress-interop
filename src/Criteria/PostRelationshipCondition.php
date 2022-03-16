<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Criteria;

final class PostRelationshipCondition
{
    public function __construct(
        private string $entityClassName,
        private array $criteria,
    ) {
    }

    public function getEntityClassName(): string
    {
        return $this->entityClassName;
    }

    public function getCriteria(): array
    {
        return $this->criteria;
    }
}
