<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;

final class EntityNotFoundException extends Exception
{
    public function __construct(string $entityClassName, array $criteria)
    {
        $message = sprintf(
            'Could not find entity "%s" with %s.',
            $entityClassName,
            implode(', ', array_map(
                static fn ($value, $field) => sprintf('%s "%s"', $field, $value),
                $criteria,
                array_keys($criteria),
            )),
        );

        parent::__construct($message);
    }
}
