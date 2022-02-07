<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\InvalidFieldNameException;
use Williarin\WordpressInterop\Exception\InvalidTypeException;
use function Williarin\WordpressInterop\Util\String\field_to_property;

/**
 * @property EntityManagerInterface $entityManager
 * @property string                 $entityClassName
 */
trait FieldValidationTrait
{
    private function validateFieldName(string $fieldName, string $fallbackEntity, string $tableName): string
    {
        $propertyName = field_to_property($fieldName);

        try {
            $expectedType = (new \ReflectionProperty($fallbackEntity, $propertyName))->getType();
        } catch (\ReflectionException $e) {
            try {
                if ($fallbackEntity === $this->getEntityClassName()) {
                    throw $e;
                }

                $expectedType = (new \ReflectionProperty($this->getEntityClassName(), $propertyName))->getType();
            } catch (\ReflectionException) {
                throw new InvalidFieldNameException(
                    $this->entityManager->getTablesPrefix() . $tableName,
                    strtolower($fieldName)
                );
            }
        }

        return $expectedType->getName();
    }

    private function validateFieldValue(string $field, mixed $value): mixed
    {
        $fallbackEntity = get_parent_class(static::class) ?: static::class;
        $expectedType = $this->validateFieldName($field, $fallbackEntity, static::TABLE_NAME);
        $resolvedValue = $value instanceof Operand ? $value->getOperand() : $value;

        if ($value instanceof Operand && $value->isLooseOperator()) {
            return $resolvedValue;
        }

        $newValueType = str_replace(
            ['integer', 'boolean', 'double'],
            ['int', 'bool', 'float'],
            gettype($resolvedValue),
        );

        if (
            (is_object($resolvedValue) && !is_subclass_of($resolvedValue, $expectedType))
            || (!is_object($resolvedValue) && $expectedType !== $newValueType)
        ) {
            throw new InvalidTypeException(strtolower($field), $expectedType, $newValueType);
        }

        if (is_array($resolvedValue)) {
            $resolvedValue = serialize($resolvedValue);
        }

        return $resolvedValue;
    }
}
