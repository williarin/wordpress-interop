<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Entity;

use AllowDynamicProperties;
use DateTimeInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Williarin\WordpressInterop\Attributes\Id;
use Williarin\WordpressInterop\Attributes\RepositoryClass;
use Williarin\WordpressInterop\Bridge\Repository\CommentRepository;

#[AllowDynamicProperties]
#[RepositoryClass(CommentRepository::class)]
class Comment
{
    use DynamicPropertiesTrait;

    #[Id]
    #[Groups('base')]
    public ?int $commentId = null;

    #[Groups('base')]
    public ?int $commentPostId = null;

    #[Groups('base')]
    public ?string $commentAuthor = null;

    #[Groups('base')]
    public ?string $commentAuthorEmail = null;

    #[Groups('base')]
    public ?string $commentAuthorUrl = null;

    #[Groups('base')]
    public ?string $commentAuthorIp = null;

    #[Groups('base')]
    public ?DateTimeInterface $commentDate = null;

    #[Groups('base')]
    public ?DateTimeInterface $commentDateGmt = null;

    #[Groups('base')]
    public ?string $commentContent = null;

    #[Groups('base')]
    public ?int $commentKarma = null;

    #[Groups('base')]
    public ?string $commentApproved = null;

    #[Groups('base')]
    public ?string $commentAgent = null;

    #[Groups('base')]
    public ?string $commentType = null;

    #[Groups('base')]
    public ?int $commentParent = null;

    #[Groups('base')]
    public ?int $userId = null;
}
