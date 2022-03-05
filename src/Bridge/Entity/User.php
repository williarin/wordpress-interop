<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Williarin\WordpressInterop\Attributes\Id;
use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\UserRepository;
use Williarin\WordpressInterop\Bridge\Type\GenericData;

#[RepositoryClass(UserRepository::class)]
class User
{
    #[Id]
    #[Groups('base')]
    public ?int $id = null;

    #[Groups('base')]
    public ?string $userLogin = null;

    #[Groups('base')]
    public ?string $userNicename = null;

    #[Groups('base')]
    public ?string $userEmail = null;

    #[Groups('base')]
    public ?string $userUrl = null;

    #[Groups('base')]
    public ?\DateTimeInterface $userRegistered = null;

    #[Groups('base')]
    public ?int $userStatus = null;

    #[Groups('base')]
    public ?string $displayName = null;

    public ?string $nickname = null;
    public ?string $firstName = null;
    public ?string $lastName = null;
    public ?string $description = null;
    public ?string $locale = null;
    public ?GenericData $capabilities = null;
    public ?string $lastUpdate = null;
    public ?string $billingFirstName = null;
    public ?string $billingLastName = null;
    public ?string $billingCompany = null;
    public ?string $billingAddress1 = null;
    public ?string $billingAddress2 = null;
    public ?string $billingCity = null;
    public ?string $billingState = null;
    public ?string $billingPostcode = null;
    public ?string $billingCountry = null;
    public ?string $billingEmail = null;
    public ?string $billingPhone = null;
    public ?string $shippingFirstName = null;
    public ?string $shippingLastName = null;
    public ?string $shippingCompany = null;
    public ?string $shippingAddress1 = null;
    public ?string $shippingAddress2 = null;
    public ?string $shippingCity = null;
    public ?string $shippingState = null;
    public ?string $shippingPostcode = null;
    public ?string $shippingCountry = null;
    public ?\DateTimeInterface $lastActive = null;
}
