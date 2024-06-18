<?php

declare(strict_types=1);

namespace Williarin\WordpressInterop\Bridge\Repository;

use Williarin\WordpressInterop\Bridge\Entity\Comment;

class CommentRepository extends AbstractEntityRepository
{
    protected const TABLE_NAME = 'comments';
    protected const TABLE_META_NAME = 'commentmeta';
    protected const TABLE_IDENTIFIER = 'comment_id';
    protected const TABLE_META_IDENTIFIER = 'comment_id';
    protected const FALLBACK_ENTITY = Comment::class;

    public function __construct()
    {
        parent::__construct(Comment::class);
    }
}
