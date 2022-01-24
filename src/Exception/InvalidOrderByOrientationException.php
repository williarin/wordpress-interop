<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;

final class InvalidOrderByOrientationException extends Exception
{
    public function __construct(string $orientation)
    {
        parent::__construct(sprintf(
            'OrderBy orientation "%s" is invalid. Please use one of "ASC" or "DESC".',
            $orientation
        ));
    }
}
