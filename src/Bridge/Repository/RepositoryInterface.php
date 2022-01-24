<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

interface RepositoryInterface
{
    public function getEntityClassName(): string;
}
