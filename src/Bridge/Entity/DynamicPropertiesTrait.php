<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use ReflectionNamedType;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;

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
                ? $reflectionType->getName()
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

    public function __isset($name): bool
    {
        return property_exists($this, $name);
    }

    public function __call(string $name, array $arguments): mixed
    {
        $propertyName = lcfirst(substr($name, 3));

        if (preg_match('/^get[A-Z]/', $name)) {
            return $this->__get($propertyName);
        }

        if (preg_match('/^set[A-Z]/', $name)) {
            if (count($arguments) !== 1) {
                throw new InvalidArgumentException('Setter needs a value as argument.');
            }

            $this->__set($propertyName, $arguments[0]);

            return $this;
        }

        throw new MethodNotFoundException(static::class, $name);
    }
}
