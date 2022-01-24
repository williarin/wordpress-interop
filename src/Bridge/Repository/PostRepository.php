<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\Post;
use Williarin\WordpressInterop\EntityManagerInterface;

/**
 * @method Post   find($id)
 * @method Post   findOneBy(array $criteria, array $orderBy = null)
 * @method Post[] findAll()
 */
final class PostRepository extends AbstractEntityRepository
{
    protected const POST_TYPE = 'post';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        parent::__construct($entityManager, $serializer, Post::class);
    }
}
