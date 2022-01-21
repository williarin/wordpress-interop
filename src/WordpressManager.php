<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop;

use Doctrine\DBAL\Connection;

final class WordpressManager implements WordpressManagerInterface
{
    public function __construct(private Connection $connection)
    {

    }
}
