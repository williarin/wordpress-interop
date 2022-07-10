<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\PostMeta;
use Williarin\WordpressInterop\Bridge\Repository\RepositoryInterface;
use Williarin\WordpressInterop\Exception\PostMetaKeyAlreadyExistsException;
use Williarin\WordpressInterop\Exception\PostMetaKeyNotFoundException;
use Williarin\WordpressInterop\Test\TestCase;

class PostMetaRepositoryTest extends TestCase
{
    private RepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->manager->getRepository(PostMeta::class);
    }

    public function testFindReturnsCorrectValue(): void
    {
        self::assertEquals('value3', $this->repository->find(11, 'key1'));
    }

    public function testFindUnserializesValue(): void
    {
        $value = $this->repository->find(13, '_wp_attachment_metadata');
        self::assertIsArray($value);
        self::assertArrayHasKey('width', $value);
    }

    public function testFindThrowsExceptionIfNotFound(): void
    {
        $this->expectException(PostMetaKeyNotFoundException::class);
        $this->repository->find(5, 'nonexistent_key');
    }

    public function testCreateNewStringValue(): void
    {
        $this->repository->delete(5, 'new_key');
        $result = $this->repository->create(5, 'new_key', 'hello');
        self::assertTrue($result);
        self::assertEquals('hello', $this->repository->find(5, 'new_key'));
    }

    public function testCreateNewArrayValue(): void
    {
        $this->repository->delete(5, 'new_serialized_key');
        $result = $this->repository->create(5, 'new_serialized_key', ['hello' => 'world']);
        self::assertTrue($result);
        self::assertEquals('a:1:{s:5:"hello";s:5:"world";}', $this->repository->find(5, 'new_serialized_key', false));
    }

    public function testCantCreateDuplicates(): void
    {
        $this->repository->delete(5, 'about_to_be_duplicated');
        $this->repository->create(5, 'about_to_be_duplicated', 'hello');

        $this->expectException(PostMetaKeyAlreadyExistsException::class);
        $this->repository->create(5, 'about_to_be_duplicated', 'world');
    }

    public function testUpdateExistingValueWithStringValue(): void
    {
        $this->repository->delete(5, 'new_unique_key');
        $this->repository->create(5, 'new_unique_key', 'hello');
        $result = $this->repository->update(5, 'new_unique_key', 'world');
        self::assertTrue($result);
        self::assertEquals('world', $this->repository->find(5, 'new_unique_key'));
    }

    public function testUpdateExistingValueWithArrayValue(): void
    {
        $this->repository->delete(5, 'new_unique_serialized_key');
        $this->repository->create(5, 'new_unique_serialized_key', 'hello');
        $result = $this->repository->update(5, 'new_unique_serialized_key', ['this_is_an' => 'array']);
        self::assertTrue($result);
        self::assertEquals('a:1:{s:10:"this_is_an";s:5:"array";}', $this->repository->find(5, 'new_unique_serialized_key', false));
    }

    public function testUpdateNonExistentKeyReturnsFalse(): void
    {
        self::assertFalse($this->repository->update(5555, 'nonexistent_key', 'world'));
    }

    public function testUpdateNonExistentKeyThrowsException(): void
    {
        $this->expectException(PostMetaKeyNotFoundException::class);
        $this->repository->update(5555, 'nonexistent_key', 'world', true);
    }

    public function testDeletePostMetaWorks(): void
    {
        $this->repository->delete(5, 'a_key_to_delete');
        $this->repository->create(5, 'a_key_to_delete', 'hello');
        $result = $this->repository->delete(5, 'a_key_to_delete');
        self::assertTrue($result);

        $this->expectException(PostMetaKeyNotFoundException::class);
        $this->repository->find(5, 'a_key_to_delete');
    }

    public function testDeleteNonExistentKeyReturnsFalse(): void
    {
        self::assertFalse($this->repository->update(4444, 'another_nonexistent_key', 'hello'));
    }

    public function testFindBy(): void
    {
        $postMetas = $this->repository->findBy(23);

        self::assertEquals([
            '_sku' => 'woo-hoodie-with-zipper',
            '_regular_price' => '45',
            '_sale_price' => '',
            '_sale_price_dates_from' => '',
            '_sale_price_dates_to' => '',
            'total_sales' => '0',
            '_tax_status' => 'taxable',
            '_tax_class' => '',
            '_manage_stock' => 'no',
            '_backorders' => 'no',
            '_low_stock_amount' => '',
            '_sold_individually' => 'no',
            '_weight' => '2',
            '_length' => '8',
            '_width' => '6',
            '_height' => '2',
            '_upsell_ids' => [],
            '_crosssell_ids' => [],
            '_purchase_note' => '',
            '_default_attributes' => [],
            '_virtual' => 'no',
            '_downloadable' => 'no',
            '_product_image_gallery' => '',
            '_download_limit' => '0',
            '_download_expiry' => '0',
            '_stock' => '',
            '_stock_status' => 'instock',
            '_wc_average_rating' => '0',
            '_wc_rating_count' => [],
            '_wc_review_count' => '0',
            '_downloadable_files' => [],
            '_product_attributes' => [],
            '_product_version' => '3.5.3',
            '_price' => '45',
            '_thumbnail_id' => '52',
        ], $postMetas);
    }

    public function testFindByWithNoResult(): void
    {
        $postMetas = $this->repository->findBy(100);
        self::assertEquals([], $postMetas);
    }
}
