<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Williarin\WordpressInterop\Bridge\Entity\Product;
use Williarin\WordpressInterop\Criteria\Operand;

/**
 * @method Product   find($id)
 * @method Product   findOneBy(array $criteria, array $orderBy = null)
 * @method Product   findOneByPostAuthor(int|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostDate(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostDateGmt(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostContent(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostTitle(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostExcerpt(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostStatus(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByCommentStatus(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPingStatus(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostPassword(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostName(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByToPing(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPinged(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostModifiedGmt(DateTimeInterface|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostParent(int|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByGuid(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByMenuOrder(int|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostType(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPostMimeType(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneBySku(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByTaxStatus(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByTaxClass(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByManageStock(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByBackorders(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByLowStockAmount(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneBySoldIndividually(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPurchaseNote(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByVirtual(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByDownloadable(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByProductImageGallery(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByDownloadExpiry(int|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByStock(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByStockStatus(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByAverageRating(int|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByReviewCount(int|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByProductVersion(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByThumbnailId(int|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByPrice(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByRegularPrice(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneBySalePrice(string|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByDownloadLimit(int|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByTotalSales(int|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByWeight(float|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByLength(float|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByWidth(float|Operand $newValue, array $orderBy = null)
 * @method Product   findOneByHeight(float|Operand $newValue, array $orderBy = null)
 * @method Product[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostAuthor(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostDate(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostDateGmt(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostContent(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostTitle(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostExcerpt(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByCommentStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPingStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostPassword(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostName(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByToPing(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPinged(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostModifiedGmt(DateTimeInterface|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostParent(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByGuid(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByMenuOrder(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostMimeType(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByCommentCount(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findBySku(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByTaxStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByTaxClass(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByManageStock(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByBackorders(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByLowStockAmount(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findBySoldIndividually(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPurchaseNote(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByVirtual(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByDownloadable(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByProductImageGallery(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByDownloadExpiry(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByStock(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByStockStatus(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByAverageRating(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByReviewCount(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByProductVersion(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByThumbnailId(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPrice(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByRegularPrice(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findBySalePrice(string|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByDownloadLimit(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByTotalSales(int|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByWeight(float|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByLength(float|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByWidth(float|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByHeight(float|Operand $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findAll(array $orderBy = null)
 */
class ProductRepository extends AbstractEntityRepository
{
    protected const MAPPED_FIELDS = [
        'sku',
        'sale_price_dates_from',
        'sale_price_dates_to',
        'tax_status',
        'tax_class',
        'manage_stock',
        'backorders',
        'low_stock_amount',
        'sold_individually',
        'weight',
        'length',
        'width',
        'height',
        'upsell_ids',
        'crosssell_ids',
        'purchase_note',
        'default_attributes',
        'virtual',
        'downloadable',
        'product_image_gallery',
        'download_limit',
        'download_expiry',
        'stock',
        'stock_status',
        '_wc_average_rating' => 'average_rating',
        '_wc_rating_count' => 'rating_count',
        '_wc_review_count' => 'review_count',
        'downloadable_files',
        'product_attributes',
        'product_version',
        'thumbnail_id',
        'price',
        'regular_price',
        'sale_price',
    ];

    public function __construct()
    {
        parent::__construct(Product::class);
    }

    protected function getPostType(): string
    {
        return 'product';
    }
}
