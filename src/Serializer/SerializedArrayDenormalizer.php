<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Serializer;

use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Williarin\WordpressInterop\Bridge\Type\AttachmentMetadata;

final class SerializedArrayDenormalizer implements ContextAwareDenormalizerInterface
{
    public function __construct(private DenormalizerInterface $denormalizer)
    {
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $type === AttachmentMetadata::class;
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $unserialized = unserialize($data, ['allowed_classes' => []]);

        if ($data === 'b:0;' || $unserialized !== false) {
            return $this->denormalizer->denormalize($unserialized, AttachmentMetadata::class);
        }

        return $data;
    }
}
