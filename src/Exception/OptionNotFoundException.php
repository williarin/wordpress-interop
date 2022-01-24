<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;

final class OptionNotFoundException extends Exception
{
    public function __construct(string $option)
    {
        parent::__construct(sprintf('Option "%s" not found.', $option));
    }
}
