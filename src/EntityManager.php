<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Williarin\WordpressInterop\Bridge\Repository\AbstractEntityRepository;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;

class EntityManager extends AbstractEntityManager
{
    protected function getRepositoryServiceForClass(?string $entityClassName): RepositoryInterface
    {
        $repositoryServiceName = $this->getRepositoryNameForClass($entityClassName);

        $repository = $repositoryServiceName
            ? new $repositoryServiceName()
            : new class($entityClassName) extends AbstractEntityRepository {
            };

        $repository->setSerializer($this->serializer);

        return $repository;
    }
}
