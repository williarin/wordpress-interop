<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Persistence;

use Symfony\Component\String\Slugger\SluggerInterface;
use Williarin\WordpressInterop\Attributes\Slug;
use Williarin\WordpressInterop\Attributes\Unique;
use Williarin\WordpressInterop\Bridge\Entity\BaseEntity;
use Williarin\WordpressInterop\Bridge\Entity\Product;
use Williarin\WordpressInterop\Bridge\Entity\Term;
use Williarin\WordpressInterop\Criteria\PostRelationshipCondition;
use Williarin\WordpressInterop\EntityManagerAwareInterface;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\EntityManagerNotSetException;
use Williarin\WordpressInterop\Exception\MissingEntityTypeException;
use function Williarin\WordpressInterop\Util\String\property_to_field;

final class DuplicationService implements DuplicationServiceInterface, EntityManagerAwareInterface
{
    private ?EntityManagerInterface $entityManager = null;

    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    public function duplicate(
        BaseEntity|int $entityOrId,
        ?string $entityType = null,
        string $postStatus = self::POST_STATUS_DRAFT,
        string $suffix = ' (Copy)',
    ): BaseEntity {
        if (!$this->entityManager) {
            throw new EntityManagerNotSetException(self::class);
        }

        if (is_int($entityOrId)) {
            if ($entityType === null) {
                throw new MissingEntityTypeException();
            }

            $entity = $this->entityManager->getRepository($entityType)
                ->find($entityOrId)
            ;
        } else {
            $entity = $entityOrId;
        }

        $clone = clone $entity;
        $clone->id = null;

        $properties = $this->getClassPropertyAttributes($clone, BaseEntity::class, $suffix);

        foreach ($properties as $property => $attributes) {
            $clone->{$property} .= \in_array(Slug::class, $attributes, true)
                ? '-' . $this->slugger->slug($suffix)
                    ->lower()
                    ->toString()
                : $suffix
            ;
        }

        $this->entityManager->persist($clone);
        $this->duplicateMeta($entity, $clone, $suffix);
        $this->duplicateTerms($entity, $clone);

        return $clone;
    }

    private function duplicateMeta(BaseEntity $entity, BaseEntity &$clone, string $suffix): void
    {
        $repository = $this->entityManager->getRepository($entity::class);
        $metaRepository = $this->entityManager->getRepository($repository->getMetaEntityClassName());

        $postMetas = $metaRepository->findBy($entity->id, false);

        $properties = $this->getClassPropertyAttributes($clone, Product::class, $suffix);

        foreach ($properties as $property => $attributes) {
            $metaKey = $repository->getMappedMetaKey(property_to_field($property));

            $postMetas[$metaKey] .= \in_array(Slug::class, $attributes, true)
                ? '-' . $this->slugger->slug($suffix)
                    ->lower()
                    ->toString()
                : $suffix
            ;
        }

        foreach ($postMetas as $key => $value) {
            $metaRepository->create($clone->id, $key, $value);
        }

        $clone = $repository->find($clone->id);
    }

    private function duplicateTerms(BaseEntity $entity, BaseEntity $clone): void
    {
        $termRepository = $this->entityManager->getRepository(Term::class);
        $terms = $termRepository->findBy([
            new PostRelationshipCondition(Product::class, [
                'id' => $entity->id,
            ]),
        ]);

        $termRepository->addTermsToEntity($clone, $terms);
    }

    private function getClassPropertyAttributes(BaseEntity $entity, string $className, string $suffix): array
    {
        $properties = [];

        foreach ((new \ReflectionClass($entity::class))->getProperties() as $property) {
            if ($property->class !== $className) {
                continue;
            }

            if (\count($property->getAttributes(Unique::class)) > 0) {
                $properties[$property->getName()] = [Unique::class];
            }

            if (\count($property->getAttributes(Slug::class)) > 0) {
                $properties[$property->getName()][] = Slug::class;
            }
        }

        return $properties;
    }
}
