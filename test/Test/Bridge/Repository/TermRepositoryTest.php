<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Term;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Bridge\Repository\TermRepository;
use Williarin\WordpressInterop\Test\TestCase;

class TermRepositoryTest extends TestCase
{
    /** @var TermRepository */
    private RepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(Term::class);
    }

    public function testFind(): void
    {
        $term = $this->repository->find(18);

        self::assertInstanceOf(Term::class, $term);
        self::assertSame('Accessories', $term->name);
    }

    public function testFindOneByAscending(): void
    {
        $term = $this->repository->findOneBy(
            ['taxonomy' => 'product_cat', 'count' => 5],
            ['term_id' => 'ASC'],
        );

        self::assertInstanceOf(Term::class, $term);
        self::assertSame('Tshirts', $term->name);
    }

    public function testFindOneByDescending(): void
    {
        $term = $this->repository->findOneBy(
            ['taxonomy' => 'product_cat', 'count' => 5],
            ['term_id' => 'DESC'],
        );

        self::assertInstanceOf(Term::class, $term);
        self::assertSame('Accessories', $term->name);
    }

    public function testFindByTaxonomy(): void
    {
        $terms = $this->repository->findByTaxonomy('product_cat');

        self::assertContainsOnlyInstancesOf(Term::class, $terms);
        self::assertSame(range(15, 21), array_column($terms, 'termId'));
    }
}
