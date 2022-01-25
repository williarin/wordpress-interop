<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\ProductRepository;
use Williarin\WordpressInterop\Bridge\Type\GenericData;

#[RepositoryClass(ProductRepository::class)]
final class Product extends BaseEntity
{
    public ?string $sku = null;
    public string|int|null $salePriceDatesFrom = null;
    public string|int|null $salePriceDatesTo = null;
    public ?int $totalSales = null;
    public ?string $taxStatus = null;
    public ?string $taxClass = null;
    public ?string $manageStock = null;
    public ?string $backorders = null;
    public ?string $lowStockAmount = null;
    public ?string $soldIndividually = null;
    public ?float $weight = null;
    public ?float $length = null;
    public ?float $width = null;
    public ?float $height = null;
    public ?GenericData $upsellIds = null;
    public ?GenericData $crosssellIds = null;
    public ?string $purchaseNote = null;
    public ?GenericData $defaultAttributes = null;
    public ?string $virtual = null;
    public ?string $downloadable = null;
    public ?string $productImageGallery = null;
    public ?int $downloadLimit = null;
    public ?int $downloadExpiry = null;
    public ?string $stock = null;
    public ?string $stockStatus = null;
    public ?int $averageRating = null;
    public ?GenericData $ratingCount = null;
    public ?int $reviewCount = null;
    public ?GenericData $downloadableFiles = null;
    public ?GenericData $productAttributes = null;
    public ?string $productVersion = null;
    public ?int $thumbnailId = null;
    public ?string $price = null;
    public ?string $regularPrice = null;
    public ?string $salePrice = null;
}
