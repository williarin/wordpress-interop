<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Williarin\WordpressInterop\Attributes\External;
use Williarin\WordpressInterop\Attributes\Id;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use function Williarin\WordpressInterop\Util\String\property_to_field;

/**
 * @property string $entityClassName
 *
 * @method QueryBuilder createFindByQueryBuilder(array $criteria, ?array $orderBy)
 */
trait FindByTrait
{
    use NormalizerTrait;

    protected ?array $entityBaseFields = null;
    protected ?array $entityExternalFields = null;
    protected ?array $entityExtraFields = null;

    public function find(int $id): mixed
    {
        $classId = $this->getEntityIdProperty();

        return $this->findOneBy([
            $classId => $id,
        ]);
    }

    public function findOneBy(array $criteria, array $orderBy = null): mixed
    {
        $result = $this->createFindByQueryBuilder($criteria, $orderBy)
            ->setMaxResults(1)
            ->setFirstResult(0)
            ->executeQuery()
            ->fetchAssociative()
        ;

        if ($result === false) {
            throw new EntityNotFoundException($this->getEntityClassName(), $criteria);
        }

        return $this->denormalize($result, $this->getEntityClassName());
    }

    public function findAll(array $orderBy = null): array
    {
        return $this->findBy([], $orderBy);
    }

    public function findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null): array
    {
        $result = $this->createFindByQueryBuilder($criteria, $orderBy)
            ->setMaxResults($limit)
            ->setFirstResult($offset ?? 0)
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        return $this->denormalize($result, $this->getEntityClassName() . '[]');
    }

    /**
     * @return string[]
     */
    protected function getEntityBaseFields(): array
    {
        if ($this->entityBaseFields === null) {
            $entityClassName = $this->getEntityClassName();
            $this->entityBaseFields = array_keys($this->serializer->normalize(new $entityClassName(), null, [
                'groups' => ['base'],
            ]));
        }

        return $this->entityBaseFields;
    }

    protected function getExternalFields(): array
    {
        if ($this->entityExternalFields === null) {
            $this->entityExternalFields = [];

            foreach ((new \ReflectionClass($this->getEntityClassName()))->getProperties() as $property) {
                if (\count($property->getAttributes(External::class)) > 0) {
                    $this->entityExternalFields[] = property_to_field($property->getName());
                }
            }
        }

        return $this->entityExternalFields;
    }

    /**
     * @return string[]
     */
    protected function getEntityExtraFields(): array
    {
        if ($this->entityExtraFields === null) {
            $baseFields = $this->getEntityBaseFields();
            $externalFields = $this->getExternalFields();
            $entityClassName = $this->getEntityClassName();
            $allFields = array_keys($this->propertyNormalizer->normalize(new $entityClassName()));
            $this->entityExtraFields = array_diff($allFields, $baseFields, $externalFields);
        }

        return $this->entityExtraFields;
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

    protected function doFindOneBy(string $name, array $arguments): mixed
    {
        $resolver = (new OptionsResolver())
            ->setRequired(['0'])
            ->setDefault('1', [])
            ->setAllowedTypes('1', 'array')
        ;

        $fieldName = property_to_field(substr($name, 9));

        $arguments = $this->validateArguments($resolver, $arguments);
        $this->validateFieldValue($fieldName, $arguments[0]);

        return $this->findOneBy([
            $fieldName => $arguments[0],
        ], $arguments[1]);
    }

    protected function doFindBy(string $name, array $arguments): array
    {
        $resolver = (new OptionsResolver())
            ->setRequired(['0'])
            ->setDefault('1', [])
            ->setDefault('2', null)
            ->setDefault('3', 0)
            ->setAllowedTypes('1', 'array')
            ->setAllowedTypes('2', ['int', 'null'])
            ->setAllowedTypes('3', 'int')
        ;

        $fieldName = property_to_field(substr($name, 6));

        $arguments = $this->validateArguments($resolver, $arguments);
        $this->validateFieldValue($fieldName, $arguments[0]);

        return $this->findBy([
            $fieldName => $arguments[0],
        ], ...array_slice($arguments, 1));
    }

    private function getEntityIdProperty(): string
    {
        foreach ((new \ReflectionClass($this->getEntityClassName()))->getProperties() as $property) {
            $attributes = array_filter(
                $property->getAttributes(Id::class),
                static fn (\ReflectionAttribute $attribute) => $attribute->getName() === Id::class,
            );

            if (!empty($attributes)) {
                return property_to_field($property->getName());
            }
        }

        return 'id';
    }

    private function validateArguments(OptionsResolver $resolver, array $arguments): array
    {
        try {
            $arguments = $resolver->resolve($arguments);
        } catch (MissingOptionsException) {
            throw new InvalidArgumentException('Some arguments are missing.');
        } catch (InvalidOptionsException) {
            throw new InvalidArgumentException('Arguments provided are of the wrong type.');
        }

        ksort($arguments);

        return $arguments;
    }
}
