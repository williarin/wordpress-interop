<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\EntityManagerInterface;

interface RepositoryInterface
{
    public function getEntityClassName(): string;

    public function setEntityManager(EntityManagerInterface $entityManager): void;

    public function setSerializer(SerializerInterface $serializer): void;
}
