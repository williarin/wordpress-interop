<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Williarin\WordpressInterop\Attributes\Id;
use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\ShopOrderItemRepository;

#[RepositoryClass(ShopOrderItemRepository::class)]
final class ShopOrderItem
{
    #[Id]
    #[Groups('base')]
    public ?int $orderItemId = null;

    #[Groups('base')]
    public ?int $orderId = null;

    #[Groups('base')]
    public ?string $orderItemName = null;

    #[Groups('base')]
    public ?string $orderItemType = null;
}
