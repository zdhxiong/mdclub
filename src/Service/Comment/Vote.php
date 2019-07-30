<?php

declare(strict_types=1);

namespace MDClub\Service\Comment;

use MDClub\Traits\Votable;

/**
 * 评论投票
 */
class Vote extends Abstracts
{
    use Votable;
}
