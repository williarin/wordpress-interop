<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test;

use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Williarin\WordpressInterop\EntityManager;

abstract class TestCase extends BaseTestCase
{
    protected EntityManager $manager;

    protected function setUp(): void
    {
        $connection = DriverManager::getConnection(['url' => getenv('WORDPRESS_DATABASE_URL')]);
        $this->manager = new EntityManager($connection);
    }
}
