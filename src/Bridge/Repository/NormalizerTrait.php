<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Criteria\NestedCondition;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;
use Williarin\WordpressInterop\Criteria\SelectColumns;

/**
 * @property SerializerInterface $serializer
 */
trait NormalizerTrait
{
    use FieldValidationTrait;

    public function denormalize(mixed $data, string $type): mixed
    {
        return $this->serializer->denormalize($data, $type, null, [
            AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
        ]);
    }

    protected function normalizeCriteria(array $criteria): array
    {
        $output = [];

        foreach ($criteria as $field => $value) {
            if ($value instanceof NestedCondition) {
                $output[] = new NestedCondition($value->getOperator(), $this->normalizeCriteria($value->getCriteria()));
            } elseif ($value instanceof RelationshipCondition || $value instanceof SelectColumns) {
                $output[] = $value;
            } else {
                $value = $this->validateFieldValue($field, $value);
                $output[$field] = (string) $this->serializer->normalize($value);
            }
        }

        return $output;
    }
}
