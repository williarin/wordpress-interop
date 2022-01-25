<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Serializer;

use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Williarin\WordpressInterop\Bridge\Type\AttachmentMetadata;
use Williarin\WordpressInterop\Bridge\Type\GenericData;
use function Williarin\WordpressInterop\Util\String\unserialize_if_needed;

final class SerializedArrayDenormalizer implements ContextAwareDenormalizerInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer
    ) {
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return in_array($type, [AttachmentMetadata::class, GenericData::class]);
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $unserialized = unserialize_if_needed($data);

        if ($data !== $unserialized) {
            if ($type === GenericData::class) {
                $unserialized = ['data' => $unserialized];
            }

            return $this->denormalizer->denormalize($unserialized, $type);
        }

        return $data;
    }
}
