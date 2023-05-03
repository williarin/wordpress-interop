<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test;

use Closure;
use PHPUnit\Framework\TestCase;
use Williarin\WordpressInterop\AbstractManagerRegistry;
use Williarin\WordpressInterop\Bridge\Entity\Product;
use Williarin\WordpressInterop\Bridge\Repository\ProductRepository;
use Williarin\WordpressInterop\EntityManagerInterface;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Persistence\DuplicationServiceInterface;

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

    public function testGetManagers(): void
    {
        $managers = $this->managerRegistry->getManagers();
        self::assertEquals(['default', 'other'], array_keys($managers));
        self::assertContainsOnlyInstancesOf(EntityManagerInterface::class, $managers);
    }

    public function testGetManager(): void
    {
        $manager = $this->managerRegistry->getManager();
        self::assertInstanceOf(EntityManagerInterface::class, $manager);
    }

    public function testGetNonExistentManager(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->managerRegistry->getManager('non_existent');
    }

    public function testGetRepository(): void
    {
        self::assertInstanceOf(ProductRepository::class, $this->managerRegistry->getRepository(Product::class));
    }

    public function testGetDefaultDuplicationService(): void
    {
        self::assertInstanceOf(DuplicationServiceInterface::class, $this->managerRegistry->getDuplicationService());
    }

    public function testGetNonExistentDuplicationService(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->managerRegistry->getDuplicationService('fr');
    }

    public function testGetDefaultDuplicationServiceFromServiceContainer(): void
    {
        self::assertInstanceOf(
            DuplicationServiceInterface::class,
            $this->managerRegistry->get(DuplicationServiceInterface::class),
        );
    }

    public function testGetNonExistentDuplicationServiceFromServiceContainer(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->managerRegistry->get(DuplicationServiceInterface::class, 'fr');
    }

    public function testGetNamedDuplicationServiceFromServiceContainer(): void
    {
        self::assertInstanceOf(
            DuplicationServiceInterface::class,
            $this->managerRegistry->get(DuplicationServiceInterface::class, 'other'),
        );
    }

    private function getManagerFactory(): Closure
    {
        return function () {
            $managerMock = $this->createMock(EntityManagerInterface::class);
            $managerMock->method('getRepository')
                ->willReturn(new ProductRepository())
            ;

            return $managerMock;
        };
    }
}

class TestManagerRegistry extends AbstractManagerRegistry
{
    /** @var EntityManagerInterface[] */
    private array $services;

    /** @var callable */
    private $managerFactory;

    public function __construct(array $managers, string $defaultManager, callable $managerFactory)
    {
        $this->managerFactory = $managerFactory;
        parent::__construct($managers, $defaultManager);
    }

    protected function getService($name): EntityManagerInterface
    {
        if (!isset($this->services[$name])) {
            $this->services[$name] = call_user_func($this->managerFactory);
        }

        return $this->services[$name];
    }
}
