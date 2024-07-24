<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Test;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ConstraintViolationListNormalizer;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Symfony\Component\Serializer\Normalizer\DateIntervalNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeZoneNormalizer;
use Symfony\Component\Serializer\Normalizer\FormErrorNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ProblemNormalizer;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Serializer\Normalizer\UnwrappingDenormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Williarin\WordpressInterop\EntityManager;
use Williarin\WordpressInterop\Serializer\SerializedArrayDenormalizer;

abstract class TestCase extends BaseTestCase
{
    protected EntityManager $manager;
    protected SerializerInterface $serializer;
    private Connection $connection;

    protected function setUp(): void
    {
        $dsnParser  = new DsnParser(['mysql' => 'pdo_mysql']);
        $this->connection = DriverManager::getConnection(
            $dsnParser->parse(getenv('WORDPRESS_DATABASE_URL'))
        );

        $loader = class_exists('\\Symfony\\Component\\Serializer\\Mapping\\Loader\\AnnotationLoader')
            ? new AnnotationLoader(new AnnotationReader())
            : new AttributeLoader();

        $objectNormalizer = new ObjectNormalizer(
            new ClassMetadataFactory($loader),
            new CamelCaseToSnakeCaseNameConverter(),
            null,
            new ReflectionExtractor()
        );

        $this->serializer = new Serializer([
            new BackedEnumNormalizer(),
            new ConstraintViolationListNormalizer(),
            new DataUriNormalizer(),
            new DateIntervalNormalizer(),
            new DateTimeZoneNormalizer(),
            new FormErrorNormalizer(),
            new JsonSerializableNormalizer(),
            new ProblemNormalizer(),
            new UidNormalizer(),
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            new UnwrappingDenormalizer(),
            new SerializedArrayDenormalizer($objectNormalizer),
            $objectNormalizer,
        ]);

        $this->manager = new EntityManager($this->connection, $this->serializer);

        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->connection->rollBack();
    }
}
