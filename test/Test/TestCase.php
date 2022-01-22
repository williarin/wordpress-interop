<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test;

use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\EntityManager;

abstract class TestCase extends BaseTestCase
{
    protected EntityManager $manager;
    protected SerializerInterface $serializer;

    protected function setUp(): void
    {
        $connection = DriverManager::getConnection(['url' => getenv('WORDPRESS_DATABASE_URL')]);
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->manager = new EntityManager($connection, $this->serializer);
    }
}
