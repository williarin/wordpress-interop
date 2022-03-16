<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Criteria\NestedCondition;
use Williarin\WordpressInterop\Criteria\Operand;
use Williarin\WordpressInterop\Criteria\PostRelationshipCondition;
use Williarin\WordpressInterop\Criteria\RelationshipCondition;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\Criteria\TermRelationshipCondition;

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

    protected function normalizeCriteria(array $criteria, string $entityClassName = null): array
    {
        $output = [];

        foreach ($criteria as $field => $value) {
            if ($value instanceof NestedCondition) {
                $output[] = new NestedCondition($value->getOperator(), $this->normalizeCriteria($value->getCriteria()));
            } elseif (
                $value instanceof RelationshipCondition
                || $value instanceof TermRelationshipCondition
                || $value instanceof PostRelationshipCondition
                || $value instanceof SelectColumns
            ) {
                $output[] = $value;
            } elseif ($value instanceof Operand && $value->isLooseOperator()) {
                $output[$field] = $value->getOperand();
            } else {
                $resolvedValue = $this->validateFieldValue($field, $value, $entityClassName);
                $output[$field] = (string) $this->serializer->normalize($resolvedValue);
            }
        }

        return $output;
    }
}
