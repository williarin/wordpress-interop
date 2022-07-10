<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

final class InvalidEntityException extends \LogicException
{
    public function __construct(mixed $entity, string $expectedClassName)
    {
        parent::__construct(sprintf('Expected "%s", got "%s".', $expectedClassName, $entity::class));
    }
}
