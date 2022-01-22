<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;

final class EntityNotFoundException extends Exception
{
    public function __construct(string $entityClassName, int $id)
    {
        parent::__construct(sprintf('Could not find entity "%s" with ID "%d"', $entityClassName, $id));
    }
}
