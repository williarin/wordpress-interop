<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Exception;

final class RelationshipIdDeprecationException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct(
            "This method is deprecated and can't be used with an Operand parameter. " .
            'Use `RelationshipCondition::getRelationshipIdOrOperand()` instead.'
        );
    }
}
