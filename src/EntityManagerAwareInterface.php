<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

interface EntityManagerAwareInterface
{
    public function setEntityManager(EntityManagerInterface $entityManager): void;
}
