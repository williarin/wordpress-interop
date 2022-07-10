<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

trait DynamicPropertiesTrait
{
    public function __set(string $property, mixed $value): void
    {
        if ($value === '' && !is_string($this->{$property})) {
            return;
        }

        try {
            $expectedType = (new \ReflectionProperty(static::class, $property))->getType()->getName();
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
