<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use DateTimeInterface;

class BaseEntity
{
    public ?int $id = null;
    public ?int $postAuthor = null;
    public ?DateTimeInterface $postDate = null;
    public ?DateTimeInterface $postDateGmt = null;
    public ?string $postContent = null;
    public ?string $postTitle = null;
    public ?string $postExcerpt = null;
    public ?string $postStatus = null;
    public ?string $commentStatus = null;
    public ?string $pingStatus = null;
    public ?string $postPassword = null;
    public ?string $postName = null;
    public ?string $toPing = null;
    public ?string $pinged = null;
    public ?DateTimeInterface $postModified = null;
    public ?DateTimeInterface $postModifiedGmt = null;
    public ?string $postContentFiltered = null;
    public ?int $postParent = null;
    public ?string $guid = null;
    public ?int $menuOrder = null;
    public ?string $postType = null;
    public ?string $postMimeType = null;
    public ?int $commentCount = null;
}
