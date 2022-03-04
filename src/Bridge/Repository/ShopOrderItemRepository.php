<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\ShopOrderItem;
use Williarin\WordpressInterop\Criteria\Operand;

/**
 * @method ShopOrderItem   find(int $id)
 * @method ShopOrderItem   findOneByOrderItemId(int|Operand $newValue, array $orderBy = null)
 * @method ShopOrderItem   findOneByOrderId(int|Operand $newValue, array $orderBy = null)
 * @method ShopOrderItem   findOneByOrderItemName(string|Operand $newValue, array $orderBy = null)
 * @method ShopOrderItem   findOneByOrderItemType(string|Operand $newValue, array $orderBy = null)
 * @method ShopOrderItem[] findByPostAuthor(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrderItem[] findByOrderId(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrderItem[] findByOrderItemName(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method ShopOrderItem[] findByOrderItemType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method bool            updateOrderId(int $id, int|Operand $newValue)
 * @method bool            updateOrderItemName(int $id, string|Operand $newValue)
 * @method bool            updateOrderItemType(int $id, string|Operand $newValue)
 */
class ShopOrderItemRepository extends AbstractEntityRepository
{
    protected const TABLE_NAME = 'woocommerce_order_items';
    protected const TABLE_META_NAME = 'woocommerce_order_itemmeta';
    protected const TABLE_IDENTIFIER = 'order_item_id';
    protected const TABLE_META_IDENTIFIER = 'order_item_id';
    protected const FALLBACK_ENTITY = ShopOrderItem::class;

    public function __construct()
    {
        parent::__construct(ShopOrderItem::class);
    }
}
