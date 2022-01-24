<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\Page;
use Williarin\WordpressInterop\EntityManagerInterface;

/**
 * @method Page|null find($id)
 * @method Page[]    findAll()
 */
final class PageRepository extends AbstractEntityRepository
{
    protected const POST_TYPE = 'page';

    public function __construct(
        protected EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        parent::__construct($entityManager, $serializer, Page::class);
    }
}
