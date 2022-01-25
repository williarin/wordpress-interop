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
 * @method Product   findOneByCommentCount(int $newValue, array $orderBy = null)
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
