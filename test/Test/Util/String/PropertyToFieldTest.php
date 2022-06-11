<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Util\String;

use PHPUnit\Framework\TestCase;

use function Williarin\WordpressInterop\Util\String\property_to_field;

class PropertyToFieldTest extends TestCase
{
    public function testCamelCase(): void
    {
        self::assertEquals('how_are_you_today', property_to_field('howAreYouToday'));
    }

    public function testSnakeCase(): void
    {
        self::assertEquals('how_are_you_today', property_to_field('how_are_you_today'));
    }
}
