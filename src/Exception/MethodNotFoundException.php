<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

use Exception;

final class MethodNotFoundException extends Exception
{
    public function __construct(string $className, string $methodName)
    {
        parent::__construct(sprintf('Method "%s" doesn\'t exist in class "%s".', $methodName, $className));
    }
}
