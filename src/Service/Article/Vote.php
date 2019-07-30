<?php

declare(strict_types=1);

namespace MDClub\Service\Article;

use MDClub\Traits\Votable;

/**
 * 文章投票
 */
class Vote extends Abstracts
{
    use Votable;
}
