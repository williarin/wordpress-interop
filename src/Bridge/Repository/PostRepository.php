<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Repository;

use Williarin\WordpressInterop\Entity\Post;
use Williarin\WordpressInterop\EntityManagerInterface;

final class PostRepository extends EntityRepository
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, Post::class);
    }
}
