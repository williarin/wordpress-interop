<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;

final class InvalidArgumentException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
