<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Criteria;

final class SelectColumns
{
    /**
     * @param string[] $columns
     */
    public function __construct(
        private array $columns
    ) {
    }

    public function getColumns(): array
    {
        return $this->columns;
    }
}
