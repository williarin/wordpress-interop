<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\ShopOrderRepository;

#[RepositoryClass(ShopOrderRepository::class)]
class ShopOrder extends BaseEntity
{
    public ?string $paymentMethod = null;
    public ?string $paypalEmail = null;
    public ?string $billingEmail = null;
    public ?string $billingFirstName = null;
    public ?string $billingLastName = null;
    public ?string $billingCompany = null;
    public ?string $billingAddress1 = null;
    public ?string $billingAddress2 = null;
    public ?string $billingCity = null;
    public ?string $billingState = null;
    public ?string $billingPostcode = null;
    public ?string $billingCountry = null;
    public ?string $billingPhone = null;
    public ?string $shippingEmail = null;
    public ?string $shippingFirstName = null;
    public ?string $shippingLastName = null;
    public ?string $shippingCompany = null;
    public ?string $shippingAddress1 = null;
    public ?string $shippingAddress2 = null;
    public ?string $shippingCity = null;
    public ?string $shippingState = null;
    public ?string $shippingPostcode = null;
    public ?string $shippingCountry = null;
    public ?string $shippingPhone = null;
    public ?string $orderTotal = null;
    public ?string $orderCurrency = null;
    public ?\DateTimeInterface $paidDate = null;
    public ?int $customerUser = null;
}
