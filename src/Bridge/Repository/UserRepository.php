<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Williarin\WordpressInterop\Bridge\Entity\User;
use Williarin\WordpressInterop\Criteria\Operand;

/**
 * @method User   find(int $id)
 * @method User   findOneByUserLogin(string|Operand $newValue, array $orderBy = null)
 * @method User   findOneByUserNicename(string|Operand $newValue, array $orderBy = null)
 * @method User   findOneByUserEmail(string|Operand $newValue, array $orderBy = null)
 * @method User   findOneByUserUrl(string|Operand $newValue, array $orderBy = null)
 * @method User   findOneByUserRegistered(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method User   findOneByUserStatus(int|Operand $newValue, array $orderBy = null)
 * @method User   findOneByDisplayName(string|Operand $newValue, array $orderBy = null)
 * @method User[] findByUserLogin(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method User[] findByUserNicename(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method User[] findByUserEmail(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method User[] findByUserUrl(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method User[] findByUserRegistered(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method User[] findByUserStatus(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method User[] findByDisplayName(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method bool   updateUserLogin(int $id, string|Operand $newValue)
 * @method bool   updateUserNicename(int $id, string|Operand $newValue)
 * @method bool   updateUserEmail(int $id, string|Operand $newValue)
 * @method bool   updateUserUrl(int $id, string|Operand $newValue)
 * @method bool   updateUserRegistered(int $id, DateTimeInterface|Operand $newValue)
 * @method bool   updateUserStatus(int $id, int|Operand $newValue)
 * @method bool   updateDisplayName(int $id, string|Operand $newValue)
 */
class UserRepository extends AbstractEntityRepository
{
    protected const TABLE_NAME = 'users';
    protected const TABLE_META_NAME = 'usermeta';
    protected const TABLE_IDENTIFIER = 'id';
    protected const TABLE_META_IDENTIFIER = 'user_id';
    protected const FALLBACK_ENTITY = User::class;

    /** @deprecated Left for BC reasons only, use getMappedFields instead */
    protected const MAPPED_FIELDS = [
        'billing_address_1' => 'billing_address_1',
        'billing_address_2' => 'billing_address_2',
        'shipping_address_1' => 'shipping_address_1',
        'shipping_address_2' => 'shipping_address_2',
        'wp_capabilities' => 'capabilities',
        'wp_last_active' => 'last_active',
    ];

    public function __construct()
    {
        parent::__construct(User::class);
    }

    protected function getMappedFields(): array
    {
        $capabilitiesKey = sprintf('%scapabilities', $this->entityManager->getTablesPrefix());

        return [
            'billing_address_1' => 'billing_address_1',
            'billing_address_2' => 'billing_address_2',
            'shipping_address_1' => 'shipping_address_1',
            'shipping_address_2' => 'shipping_address_2',
            $capabilitiesKey => 'capabilities',
            'wp_last_active' => 'last_active',
        ];
    }
}
