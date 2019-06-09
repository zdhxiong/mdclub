<?php

declare(strict_types=1);

namespace App\Service\Comment;

use App\Traits\Votable;

/**
 * 评论投票
 */
class Vote extends Abstracts
{
    use Votable;
}
