<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Bridge\Type;

use Williarin\WordpressInterop\Bridge\Type\GenericData;
use PHPUnit\Framework\TestCase;
use Williarin\WordpressInterop\Exception\InvalidArgumentException;
use Williarin\WordpressInterop\Exception\MethodNotFoundException;

class GenericDataTest extends TestCase
{
    public function testGetRawFieldName(): void
    {
        $genericData = new GenericData();
        $genericData->data = ['sample' => 12, 'another_field' => ['an array']];
        self::assertSame(12, $genericData->sample);
        self::assertSame(['an array'], $genericData->another_field);
    }

    public function testGetCamelCaseProperty(): void
    {
        $genericData = new GenericData();
        $genericData->data = ['snake_case' => 'it works'];
        self::assertSame('it works', $genericData->snakeCase);
    }

    public function testGetGetterWithLowerCaseField(): void
    {
        $genericData = new GenericData();
        $genericData->data = ['sample' => 5];
        self::assertSame(5, $genericData->getSample());
    }

    public function testGetGetterWithUpperCaseFirstLetterField(): void
    {
        $genericData = new GenericData();
        $genericData->data = ['Something' => 'ok'];
        self::assertSame('ok', $genericData->getSomething());
    }

    public function testGetGetterWithSnakeCaseField(): void
    {
        $genericData = new GenericData();
        $genericData->data = ['snake_case' => 'it works'];
        self::assertSame('it works', $genericData->getSnakeCase());
    }

    public function testGetGetterWithPascalCaseField(): void
    {
        $genericData = new GenericData();
        $genericData->data = ['PascalCase' => 'not bad'];
        self::assertSame('not bad', $genericData->getPascalCase());
    }

    public function testGetGetterWithCamelCaseField(): void
    {
        $genericData = new GenericData();
        $genericData->data = ['camelCase' => 'works too!'];
        self::assertSame('works too!', $genericData->getCamelCase());
    }

    public function testCallRandomMethod(): void
    {
        $genericData = new GenericData();
        $this->expectException(MethodNotFoundException::class);
        $genericData->randomMethod();
    }

    public function testGetInvalidProperty(): void
    {
        $genericData = new GenericData();
        $genericData->data = ['test' => 'this is a test'];
        $this->expectException(InvalidArgumentException::class);
        $genericData->invalidProperty;
    }

    public function testGetInvalidPropertyGetter(): void
    {
        $genericData = new GenericData();
        $genericData->data = ['test' => 'this is a test'];
        $this->expectException(InvalidArgumentException::class);
        $genericData->getInvalidMethod();
    }
}
