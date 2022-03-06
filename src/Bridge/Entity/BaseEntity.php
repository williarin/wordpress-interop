<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;

abstract class BaseEntity
{
    #[Groups('base')]
    public ?int $id = null;

    #[Groups('base')]
    public ?int $postAuthor = null;

    #[Groups('base')]
    public ?DateTimeInterface $postDate = null;

    #[Groups('base')]
    public ?DateTimeInterface $postDateGmt = null;

    #[Groups('base')]
    public ?string $postContent = null;

    #[Groups('base')]
    public ?string $postTitle = null;

    #[Groups('base')]
    public ?string $postExcerpt = null;

    #[Groups('base')]
    public ?string $postStatus = null;

    #[Groups('base')]
    public ?string $commentStatus = null;

    #[Groups('base')]
    public ?string $pingStatus = null;

    #[Groups('base')]
    public ?string $postPassword = null;

    #[Groups('base')]
    public ?string $postName = null;

    #[Groups('base')]
    public ?string $toPing = null;

    #[Groups('base')]
    public ?string $pinged = null;

    #[Groups('base')]
    public ?DateTimeInterface $postModified = null;

    #[Groups('base')]
    public ?DateTimeInterface $postModifiedGmt = null;

    #[Groups('base')]
    public ?string $postContentFiltered = null;

    #[Groups('base')]
    public ?int $postParent = null;

    #[Groups('base')]
    public ?string $guid = null;

    #[Groups('base')]
    public ?int $menuOrder = null;

    #[Groups('base')]
    public ?string $postType = null;

    #[Groups('base')]
    public ?string $postMimeType = null;

    #[Groups('base')]
    public ?int $commentCount = null;

    public function __set(string $property, mixed $value): void
    {
        if ($value === '' && !is_string($this->{$property})) {
            return;
        }

        try {
            $expectedType = (new \ReflectionProperty(static::class, $property))->getType()->getName();
            settype($value, $expectedType);
        } catch (\ReflectionException) {
        }

        $this->{$property} = $value;
    }

    public function __get(string $property): mixed
    {
        return $this->{$property};
    }
}
