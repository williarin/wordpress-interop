<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

final class EntityManagerNotSetException extends \RuntimeException
{
    public function __construct(string $owningClass)
    {
        parent::__construct(sprintf('You must call %s::setEntityManager() before use.', $owningClass));
    }
}
