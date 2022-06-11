<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Util\String;

use PHPUnit\Framework\TestCase;

use function Williarin\WordpressInterop\Util\String\field_to_property;

class FieldToPropertyTest extends TestCase
{
    public function testSnakeCase(): void
    {
        self::assertEquals('howAreYouToday', field_to_property('how_are_you_today'));
    }

    public function testPrefixedSnakeCase(): void
    {
        self::assertEquals('howAreYouToday', field_to_property('p0.how_are_you_today'));
    }
}
