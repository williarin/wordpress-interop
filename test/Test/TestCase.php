<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\EntityManager;
use Williarin\WordpressInterop\Serializer\SerializedArrayDenormalizer;

abstract class TestCase extends BaseTestCase
{
    protected EntityManager $manager;
    protected SerializerInterface $serializer;

    protected function setUp(): void
    {
        $connection = DriverManager::getConnection(['url' => getenv('WORDPRESS_DATABASE_URL')]);
        $objectNormalizer = new ObjectNormalizer(
            new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())),
            new CamelCaseToSnakeCaseNameConverter(),
            null,
            new ReflectionExtractor()
        );

        $this->serializer = new Serializer([
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            new SerializedArrayDenormalizer($objectNormalizer),
            $objectNormalizer,
        ]);
        $this->manager = new EntityManager($connection, $this->serializer);
    }
}
