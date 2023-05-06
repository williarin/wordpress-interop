<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use ReflectionNamedType;

trait DynamicPropertiesTrait
{
    public function __set(string $property, mixed $value): void
    {
        if ($value === '' && !is_string($this->{$property})) {
            return;
        }

        try {
            $reflectionType = (new \ReflectionProperty(static::class, $property))->getType();

            $expectedType = $reflectionType instanceof ReflectionNamedType
                ? $reflectionType?->getName()
                : (string) $reflectionType;

            settype($value, $expectedType);
        } catch (\ReflectionException) {
        }

        $this->{$property} = $value;
    }

    public function __get(string $property): mixed
    {
        return $this->{$property};
    }
}
