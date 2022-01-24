<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

final class InvalidFieldNameException extends \Exception
{
    public function __construct(string $table, string $field)
    {
        parent::__construct(sprintf('Field "%s" doesn\'t exist in table "%s".', $field, $table));
    }
}
