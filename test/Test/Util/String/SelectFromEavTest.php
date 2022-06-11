<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Util\String;

use PHPUnit\Framework\TestCase;

use function Williarin\WordpressInterop\Util\String\select_from_eav;

class SelectFromEavTest extends TestCase
{
    public function testSnakeCase(): void
    {
        self::assertEquals(
            "MAX(CASE WHEN pm_self.meta_key = '_product_attributes' THEN pm_self.meta_value END) `product_attributes`",
            select_from_eav('product_attributes'),
        );
    }

    public function testCamelCase(): void
    {
        self::assertEquals(
            "MAX(CASE WHEN pm_self.meta_key = '_product_attributes' THEN pm_self.meta_value END) `product_attributes`",
            select_from_eav('productAttributes'),
        );
    }

    public function testMetaKey(): void
    {
        self::assertEquals(
            "MAX(CASE WHEN pm_self.meta_key = 'some_key' THEN pm_self.meta_value END) `product_attributes`",
            select_from_eav('product_attributes', 'some_key'),
        );
    }

    public function testJoinTableName(): void
    {
        self::assertEquals(
            "MAX(CASE WHEN some_table.meta_key = 'some_key' THEN some_table.meta_value END) `product_attributes`",
            select_from_eav('product_attributes', 'some_key', 'some_table'),
        );
    }
}
