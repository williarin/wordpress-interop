<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Util\String;

use PHPUnit\Framework\TestCase;

use function Williarin\WordpressInterop\Util\String\unserialize_if_needed;

class UnserializeIfNeededTest extends TestCase
{
    public function testString(): void
    {
        self::assertEquals('this is a string', unserialize_if_needed('this is a string'));
    }

    public function testSerializedArray(): void
    {
        self::assertEquals(['hello' => 'world'], unserialize_if_needed(serialize(['hello' => 'world'])));
    }

    public function testNull(): void
    {
        self::assertNull(unserialize_if_needed(null));
    }
}
