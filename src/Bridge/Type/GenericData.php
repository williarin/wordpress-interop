<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Type;

use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;
use function Williarin\WordpressInterop\Util\String\property_to_field;

final class GenericData
{
    public ?array $data = null;

    public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $fieldName = property_to_field($name);

        if (array_key_exists($fieldName, $this->data)) {
            return $this->data[$fieldName];
        }

        throw new InvalidArgumentException(sprintf('Property "%s" does not exist.', $name,));
    }

    public function __call(string $name, array $arguments)
    {
        if (!str_starts_with($name, 'get')) {
            throw new MethodNotFoundException(self::class, $name);
        }

        $name = substr($name, 3);

        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        if (array_key_exists(lcfirst($name), $this->data)) {
            return $this->data[lcfirst($name)];
        }

        $fieldName = property_to_field($name);

        if (array_key_exists($fieldName, $this->data)) {
            return $this->data[$fieldName];
        }

        throw new InvalidArgumentException(sprintf('Property "%s" does not exist.', $name,));
    }
}
