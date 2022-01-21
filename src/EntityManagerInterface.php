<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Williarin\WordpressInterop\Repository\RepositoryInterface;

interface EntityManagerInterface
{
    public function addRepository(RepositoryInterface $repository, string $entityClassName): self;

    public function getRepositories(): array;

    public function getRepository(string $entityClassName): RepositoryInterface;
}
