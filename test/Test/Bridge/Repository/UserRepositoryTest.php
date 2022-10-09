<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\User;
use Williarin\WordpressInterop\Bridge\Repository\UserRepository;
use Williarin\WordpressInterop\EntityManager;
use Williarin\WordpressInterop\Exception\EntityNotFoundException;
use Williarin\WordpressInterop\Test\TestCase;

class UserRepositoryTest extends TestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(User::class);
    }

    public function testFindReturnsCorrectUser(): void
    {
        $user = $this->repository->find(3);
        self::assertInstanceOf(User::class, $user);

        $actual = $this->serializer->normalize($user);
        unset($actual['user_registered'], $actual['last_update']);

        self::assertEquals([
            'id' => 3,
            'user_login' => 'justin',
            'user_nicename' => 'justin',
            'user_email' => 'justin@woo.local',
            'user_url' => '',
            'user_status' => 0,
            'display_name' => 'justin',
            'nickname' => 'justin',
            'first_name' => '',
            'last_name' => '',
            'description' => '',
            'locale' => '',
            'capabilities' => [
                'data' => [
                    'customer' => true,
                ],
            ],
            'billing_first_name' => 'Justin',
            'billing_last_name' => 'Hills',
            'billing_company' => 'Google',
            'billing_address1' => null,
            'billing_address2' => null,
            'billing_city' => 'Dallas',
            'billing_state' => 'Texas',
            'billing_postcode' => '75204',
            'billing_country' => 'United States',
            'billing_email' => 'justin@woo.local',
            'billing_phone' => '214-927-9108',
            'shipping_first_name' => 'Justin',
            'shipping_last_name' => 'Hills',
            'shipping_company' => 'Google',
            'shipping_address1' => null,
            'shipping_address2' => null,
            'shipping_city' => 'Dallas',
            'shipping_state' => 'Texas',
            'shipping_postcode' => '75204',
            'shipping_country' => 'United States',
            'last_active' => null,
        ], $actual);
    }

    public function testFindThrowsExceptionIfNotFound(): void
    {
        $this->expectException(EntityNotFoundException::class);
        $this->repository->find(150);
    }

    public function testFindAllReturnsCorrectNumberOfUsers(): void
    {
        $users = $this->repository->findAll();
        self::assertContainsOnlyInstancesOf(User::class, $users);
        self::assertCount(4, $users);
    }

    public function testFindOneBy(): void
    {
        $user = $this->repository->findOneByDisplayName('Shop Manager');
        self::assertSame(2, $user->id);
    }

    public function testUpdateField(): void
    {
        $this->repository->updateDisplayName(4, 'Elon Musk');
        $user = $this->repository->find(4);
        self::assertSame('Elon Musk', $user->displayName);
    }

    public function testFindOneByEavAttribute(): void
    {
        $user = $this->repository->findOneByShippingState('North Dakota');
        self::assertSame(4, $user->id);
    }

    public function testFieldMapping(): void
    {
        $repository = new UserRepository();
        $repository->setEntityManager(new class extends EntityManager {
            public function __construct() {}
            public function getTablesPrefix(): string { return 'foo_'; }
        });

        $this->assertEquals('billing_address_1', $repository->getMappedMetaKey('billing_address_1'));
        $this->assertEquals('billing_address_2', $repository->getMappedMetaKey('billing_address_2'));
        $this->assertEquals('shipping_address_1', $repository->getMappedMetaKey('shipping_address_1'));
        $this->assertEquals('shipping_address_2', $repository->getMappedMetaKey('shipping_address_2'));
        $this->assertEquals('foo_capabilities', $repository->getMappedMetaKey('capabilities'));
        $this->assertEquals('wp_last_active', $repository->getMappedMetaKey('last_active'));
    }
}
