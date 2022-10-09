<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Williarin\WordpressInterop\Bridge\Entity\ShopOrder;

/**
 * @method ShopOrder   find($id)
 * @method ShopOrder   findOneBy(array $criteria, array $orderBy = null)
 * @method ShopOrder   findOneByPostAuthor(int $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostDate(DateTimeInterface $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostContent(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostTitle(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostExcerpt(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostStatus(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByCommentStatus(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPingStatus(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostPassword(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostName(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByToPing(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPinged(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostParent(int $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByGuid(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByMenuOrder(int $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostType(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByPostMimeType(string $newValue, array $orderBy = null)
 * @method ShopOrder   findOneByCommentCount(int $newValue, array $orderBy = null)
 * @method ShopOrder[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostAuthor(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostDate(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostContent(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostTitle(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostExcerpt(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByCommentStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPingStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostPassword(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostName(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByToPing(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPinged(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostParent(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByGuid(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByMenuOrder(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByPostMimeType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findByCommentCount(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrder[] findAll()
 */
class ShopOrderRepository extends AbstractEntityRepository
{
    /** @deprecated Left for BC reasons only, use getMappedFields instead */
    protected const MAPPED_FIELDS = [
        'payment_method',
        'billing_email',
        'billing_first_name',
        'billing_last_name',
        'billing_company',
        '_billing_address_1' => 'billing_address1',
        '_billing_address_2' => 'billing_address2',
        'billing_city',
        'billing_state',
        'billing_postcode',
        'billing_country',
        'billing_phone',
        'shipping_email',
        'shipping_first_name',
        'shipping_last_name',
        'shipping_company',
        '_shipping_address_1' => 'shipping_address1',
        '_shipping_address_2' => 'shipping_address2',
        'shipping_city',
        'shipping_state',
        'shipping_postcode',
        'shipping_country',
        'shipping_phone',
        'order_total',
        'order_currency',
        'paid_date',
        'customer_user',
    ];

    public function __construct()
    {
        parent::__construct(ShopOrder::class);
    }

    public function getPostType(): string
    {
        return 'shop_order';
    }

    protected function getMappedFields(): array
    {
        return self::MAPPED_FIELDS;
    }
}
