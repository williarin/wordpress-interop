<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

final class MissingEntityTypeException extends \LogicException
{
    public function __construct()
    {
        parent::__construct('Entity type must be provided to duplicate an entity by its ID.');
    }
}
