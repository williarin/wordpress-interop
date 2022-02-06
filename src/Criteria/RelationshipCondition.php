<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Criteria;

final class RelationshipCondition
{
    public function __construct(
        private int $relationshipId,
        private string $relationshipFieldName
    ) {
    }

    public function getRelationshipId(): int
    {
        return $this->relationshipId;
    }

    public function getRelationshipFieldName(): string
    {
        return $this->relationshipFieldName;
    }
}
