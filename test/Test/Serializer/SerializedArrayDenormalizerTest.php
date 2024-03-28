<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test\Serializer;

use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Williarin\WordpressInterop\Bridge\Type\GenericData;
use Williarin\WordpressInterop\Serializer\SerializedArrayDenormalizer;

final class SerializedArrayDenormalizerTest extends TestCase
{
    private SerializedArrayDenormalizer $denormalizer;

    protected function setUp(): void
    {
        $loader = class_exists('\\Symfony\\Component\\Serializer\\Mapping\\Loader\\AnnotationLoader')
            ? new AnnotationLoader(new AnnotationReader())
            : new AttributeLoader();

        $objectNormalizer = new ObjectNormalizer(
            new ClassMetadataFactory($loader),
            new CamelCaseToSnakeCaseNameConverter(),
            null,
            new ReflectionExtractor()
        );

        $this->denormalizer = new SerializedArrayDenormalizer($objectNormalizer);
    }

    public function testDenormalizeString(): void
    {
        self::assertSame('string', $this->denormalizer->denormalize('string', GenericData::class));
    }

    public function testDenormalizeSerializedArray(): void
    {
        $expected = new GenericData();
        $expected->data = ['test' => 'this is a test'];

        self::assertEquals(
            $expected,
            $this->denormalizer->denormalize('a:1:{s:4:"test";s:14:"this is a test";}', GenericData::class),
        );
    }
}
