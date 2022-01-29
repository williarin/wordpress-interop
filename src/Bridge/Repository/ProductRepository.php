<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use DateTimeInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\Bridge\Entity\Product;
use Williarin\WordpressInterop\EntityManagerInterface;

/**
 * @method Product   find($id)
 * @method Product   findOneBy(array $criteria, array $orderBy = null)
 * @method Product   findOneByPostAuthor(int $newValue, array $orderBy = null)
 * @method Product   findOneByPostDate(DateTimeInterface $newValue, array $orderBy = null)
 * @method Product   findOneByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method Product   findOneByPostContent(string $newValue, array $orderBy = null)
 * @method Product   findOneByPostTitle(string $newValue, array $orderBy = null)
 * @method Product   findOneByPostExcerpt(string $newValue, array $orderBy = null)
 * @method Product   findOneByPostStatus(string $newValue, array $orderBy = null)
 * @method Product   findOneByCommentStatus(string $newValue, array $orderBy = null)
 * @method Product   findOneByPingStatus(string $newValue, array $orderBy = null)
 * @method Product   findOneByPostPassword(string $newValue, array $orderBy = null)
 * @method Product   findOneByPostName(string $newValue, array $orderBy = null)
 * @method Product   findOneByToPing(string $newValue, array $orderBy = null)
 * @method Product   findOneByPinged(string $newValue, array $orderBy = null)
 * @method Product   findOneByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null)
 * @method Product   findOneByPostParent(int $newValue, array $orderBy = null)
 * @method Product   findOneByGuid(string $newValue, array $orderBy = null)
 * @method Product   findOneByMenuOrder(int $newValue, array $orderBy = null)
 * @method Product   findOneByPostType(string $newValue, array $orderBy = null)
 * @method Product   findOneByPostMimeType(string $newValue, array $orderBy = null)
 * @method Product   findOneBySku(string $newValue, array $orderBy = null)
 * @method Product   findOneByTaxStatus(string $newValue, array $orderBy = null)
 * @method Product   findOneByTaxClass(string $newValue, array $orderBy = null)
 * @method Product   findOneByManageStock(string $newValue, array $orderBy = null)
 * @method Product   findOneByBackorders(string $newValue, array $orderBy = null)
 * @method Product   findOneByLowStockAmount(string $newValue, array $orderBy = null)
 * @method Product   findOneBySoldIndividually(string $newValue, array $orderBy = null)
 * @method Product   findOneByPurchaseNote(string $newValue, array $orderBy = null)
 * @method Product   findOneByVirtual(string $newValue, array $orderBy = null)
 * @method Product   findOneByDownloadable(string $newValue, array $orderBy = null)
 * @method Product   findOneByProductImageGallery(string $newValue, array $orderBy = null)
 * @method Product   findOneByDownloadExpiry(int $newValue, array $orderBy = null)
 * @method Product   findOneByStock(string $newValue, array $orderBy = null)
 * @method Product   findOneByStockStatus(string $newValue, array $orderBy = null)
 * @method Product   findOneByAverageRating(int $newValue, array $orderBy = null)
 * @method Product   findOneByReviewCount(int $newValue, array $orderBy = null)
 * @method Product   findOneByProductVersion(string $newValue, array $orderBy = null)
 * @method Product   findOneByThumbnailId(int $newValue, array $orderBy = null)
 * @method Product   findOneByPrice(string $newValue, array $orderBy = null)
 * @method Product   findOneByRegularPrice(string $newValue, array $orderBy = null)
 * @method Product   findOneBySalePrice(string $newValue, array $orderBy = null)
 * @method Product   findOneByDownloadLimit(int $newValue, array $orderBy = null)
 * @method Product   findOneByTotalSales(int $newValue, array $orderBy = null)
 * @method Product   findOneByWeight(float $newValue, array $orderBy = null)
 * @method Product   findOneByLength(float $newValue, array $orderBy = null)
 * @method Product   findOneByWidth(float $newValue, array $orderBy = null)
 * @method Product   findOneByHeight(float $newValue, array $orderBy = null)
 * @method Product[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostAuthor(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostDate(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostDateGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostContent(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostTitle(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostExcerpt(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByCommentStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPingStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostPassword(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostName(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByToPing(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPinged(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostModifiedGmt(DateTimeInterface $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostParent(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByGuid(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByMenuOrder(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPostMimeType(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByCommentCount(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findBySku(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByTaxStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByTaxClass(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByManageStock(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByBackorders(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByLowStockAmount(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findBySoldIndividually(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPurchaseNote(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByVirtual(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByDownloadable(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByProductImageGallery(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByDownloadExpiry(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByStock(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByStockStatus(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByAverageRating(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByReviewCount(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByProductVersion(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByThumbnailId(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByPrice(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByRegularPrice(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findBySalePrice(string $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByDownloadLimit(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByTotalSales(int $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByWeight(float $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByLength(float $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByWidth(float $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findByHeight(float $newValue, array $orderBy = null, ?int $limit = null, int $offset = null)
 * @method Product[] findAll()
 */
final class ProductRepository extends AbstractEntityRepository
{
    protected const POST_TYPE = 'product';

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

    public function __construct(
        protected EntityManagerInterface $entityManager,
        SerializerInterface $serializer
    ) {
        parent::__construct($entityManager, $serializer, Product::class);
    }
}
