<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Williarin\WordpressInterop\Attributes\External;
use function Williarin\WordpressInterop\Util\String\property_to_field;

/**
 * @property PropertyNormalizer $propertyNormalizer
 *
 * @method string getEntityClassName()
 */
trait EntityPropertiesTrait
{
    protected array $entityBaseFields = [];
    protected array $entityExternalFields = [];
    protected array $entityExtraFields = [];

    /**
     * @return string[]
     */
    protected function getEntityBaseFields(string $entityClassName = null): array
    {
        $entityClassName = $entityClassName ?? $this->getEntityClassName();

        if (!array_key_exists($entityClassName, $this->entityBaseFields)) {
            $this->entityBaseFields[$entityClassName] = array_keys(
                $this->serializer->normalize(new $entityClassName(), null, [
                    'groups' => ['base'],
                ])
            );
        }

        return $this->entityBaseFields[$entityClassName];
    }

    protected function getExternalFields(string $entityClassName = null): array
    {
        $entityClassName = $entityClassName ?? $this->getEntityClassName();

        if (!array_key_exists($entityClassName, $this->entityExternalFields)) {
            $this->entityExternalFields[$entityClassName] = [];

            foreach ((new \ReflectionClass($entityClassName))->getProperties() as $property) {
                if (\count($property->getAttributes(External::class)) > 0) {
                    $this->entityExternalFields[$entityClassName][] = property_to_field($property->getName());
                }
            }
        }

        return $this->entityExternalFields[$entityClassName];
    }

    /**
     * @return string[]
     */
    protected function getEntityExtraFields(string $entityClassName = null): array
    {
        $entityClassName = $entityClassName ?? $this->getEntityClassName();

        if (!array_key_exists($entityClassName, $this->entityExtraFields)) {
            $baseFields = $this->getEntityBaseFields($entityClassName);
            $externalFields = $this->getExternalFields($entityClassName);
            $allFields = array_keys($this->propertyNormalizer->normalize(new $entityClassName()));
            $this->entityExtraFields[$entityClassName] = array_diff($allFields, $baseFields, $externalFields);
        }

        return $this->entityExtraFields[$entityClassName];
    }

    protected function addEntityExtraField(string $fieldName, string $entityClassName = null): self
    {
        if (!array_key_exists($entityClassName, $this->entityExtraFields)) {
            $this->getEntityExtraFields($entityClassName);
        }

        $this->entityExtraFields[$entityClassName][] = $fieldName;

        return $this;
    }

    /**
     * @return string[]
     */
    protected function getPrefixedEntityBaseFields(string $prefix): array
    {
        return array_map(
            static fn (string $property): string => sprintf('%s.%s', $prefix, $property),
            $this->getEntityBaseFields(),
        );
    }
}
