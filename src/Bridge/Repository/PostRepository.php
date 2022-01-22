<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Repository\EntityRepository;

/**
 * @method Post|null find($id)
 * @method Post[]    findAll()
 */
final class PostRepository extends EntityRepository
{
    protected const POST_TYPE = 'post';

    public function __construct(protected EntityManagerInterface $entityManager, SerializerInterface $serializer)
    {
        parent::__construct($entityManager, $serializer, Post::class);
    }
}
