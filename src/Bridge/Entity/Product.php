<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Attributes\Slug;
use Williarin\WordpressInterop\Attributes\Unique;
use Williarin\WordpressInterop\Bridge\Repository\ProductRepository;
use Williarin\WordpressInterop\Bridge\Type\GenericData;

/**
 * @property ?int   $thumbnailId
 * @property ?int   $downloadExpiry
 * @property ?int   $downloadLimit
 * @property ?int   $salePriceDatesFrom
 * @property ?int   $salePriceDatesTo
 * @property ?int   $totalSales
 * @property ?float $weight
 * @property ?float $length
 * @property ?float $width
 * @property ?float $height
 * @property ?int   $reviewCount
 * @property ?int   $averageRating
 */
#[RepositoryClass(ProductRepository::class)]
class Product extends BaseEntity
{
    #[Unique, Slug]
    public ?string $sku = null;
    public ?string $taxStatus = null;
    public ?string $taxClass = null;
    public ?string $manageStock = null;
    public ?string $backorders = null;
    public ?string $lowStockAmount = null;
    public ?string $soldIndividually = null;
    public ?GenericData $upsellIds = null;
    public ?GenericData $crosssellIds = null;
    public ?string $purchaseNote = null;
    public ?GenericData $defaultAttributes = null;
    public ?string $virtual = null;
    public ?string $downloadable = null;
    public ?string $productImageGallery = null;
    public ?string $stock = null;
    public ?string $stockStatus = null;
    public ?GenericData $ratingCount = null;
    public ?GenericData $downloadableFiles = null;
    public ?GenericData $productAttributes = null;
    public ?string $productVersion = null;
    public ?string $price = null;
    public ?string $regularPrice = null;
    public ?string $salePrice = null;
    protected ?int $downloadExpiry = null;
    protected ?int $downloadLimit = null;
    protected ?int $salePriceDatesFrom = null;
    protected ?int $salePriceDatesTo = null;
    protected ?int $totalSales = null;
    protected ?float $weight = null;
    protected ?float $length = null;
    protected ?float $width = null;
    protected ?float $height = null;
    protected ?int $thumbnailId = null;
    protected ?int $reviewCount = null;
    protected ?int $averageRating = null;
}
