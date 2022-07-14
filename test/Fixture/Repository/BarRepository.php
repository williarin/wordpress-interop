<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Fixture\Repository;

use Williarin\WordpressInterop\Bridge\Repository\AbstractEntityRepository;
use Williarin\WordpressInterop\Test\Fixture\Entity\Bar;

final class BarRepository extends AbstractEntityRepository
{
    public function __construct()
    {
        parent::__construct(Bar::class);
    }

    public function getBarTerms(): array
    {
        return ['blue', 'green', 'red'];
    }
}
