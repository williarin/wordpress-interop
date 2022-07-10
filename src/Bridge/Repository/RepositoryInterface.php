<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\SerializerAwareInterface;
use Williarin\WordpressInterop\EntityManagerAwareInterface;

interface RepositoryInterface extends EntityManagerAwareInterface, SerializerAwareInterface
{
    public function getEntityClassName(): string;
}
