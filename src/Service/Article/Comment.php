<?php

declare(strict_types=1);

namespace App\Service\Article;

use App\Traits\Commentable;

/**
 * 文章评论
 */
class Comment extends Abstracts
{
    use Commentable;
}
