<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Term;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Bridge\Repository\TermRepository;
use Williarin\WordpressInterop\Criteria\SelectColumns;
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
        self::assertSame(16, $term->termId);
        self::assertSame('Tshirts', $term->name);
        self::assertSame('tshirts', $term->slug);
        self::assertSame('product_cat', $term->taxonomy);
        self::assertSame(5, $term->count);
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

    public function testFindOneBySelectColumn(): void
    {
        $term = $this->repository->findOneBy([
            new SelectColumns(['term_id', 'name']),
            'term_id' => 16,
        ]);

        $expected = new Term();
        $expected->termId = 16;
        $expected->name = 'Tshirts';

        self::assertEquals($expected, $term);
    }
}
