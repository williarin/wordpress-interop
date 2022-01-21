<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Tests;

use Closure;
use PHPUnit\Framework\TestCase;
use Williarin\WordpressInterop\AbstractWordpressManagerRegistry;
use Williarin\WordpressInterop\WordpressManagerInterface;

class ManagerRegistryTest extends TestCase
{
    private TestManagerRegistry $managerRegistry;

    protected function setUp(): void
    {
        $this->managerRegistry = new TestManagerRegistry(
            [
                'default' => 'default_manager',
                'other' => 'other_manager',
            ],
            'default',
            $this->getManagerFactory(),
        );
    }

    public function testGetDefaultManagerName(): void
    {
        self::assertEquals('default', $this->managerRegistry->getDefaultManagerName());
    }

    public function testGetManagerNames(): void
    {
        self::assertEquals([
            'default' => 'default_manager',
            'other' => 'other_manager',
        ], $this->managerRegistry->getManagerNames());
    }

    public function testGetDefaultManager(): void
    {
        self::assertInstanceOf(WordpressManagerInterface::class, $this->managerRegistry->getManager());
    }

    public function testGetManager(): void
    {
        self::assertInstanceOf(WordpressManagerInterface::class, $this->managerRegistry->getManager('default'));
    }

    public function testGetManagers(): void
    {
        $managers = $this->managerRegistry->getManagers();
        self::assertEquals(['default', 'other'], array_keys($managers));
        self::assertContainsOnlyInstancesOf(WordpressManagerInterface::class, $managers);
    }

    private function getManagerFactory(): Closure
    {
        return function () {
            return $this->createMock(WordpressManagerInterface::class);
        };
    }
}

class TestManagerRegistry extends AbstractWordpressManagerRegistry
{
    /** @var WordpressManagerInterface[] */
    private array $services;

    /** @var callable */
    private $managerFactory;

    public function __construct(array $managers, string $defaultManager, callable $managerFactory)
    {
        $this->managerFactory = $managerFactory;
        parent::__construct($managers, $defaultManager);
    }

    protected function getService($name): WordpressManagerInterface
    {
        if (!isset($this->services[$name])) {
            $this->services[$name] = call_user_func($this->managerFactory);
        }

        return $this->services[$name];
    }
}
