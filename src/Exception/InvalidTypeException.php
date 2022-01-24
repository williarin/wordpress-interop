<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;

final class InvalidTypeException extends Exception
{
    public function __construct(string $fieldName, string $expectedType, string $actualType)
    {
        parent::__construct(sprintf(
            'Field "%s" should be of type "%s", "%s" given.',
            $fieldName,
            $expectedType,
            $actualType,
        ));
    }
}
