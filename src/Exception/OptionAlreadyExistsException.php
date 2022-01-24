<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;

final class OptionAlreadyExistsException extends Exception
{
    public function __construct(string $optionName)
    {
        parent::__construct(sprintf('Option "%s" already exists.', $optionName));
    }
}
