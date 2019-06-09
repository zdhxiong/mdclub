<?php

declare(strict_types=1);

namespace App\Service\Article;

use App\Traits\Votable;

/**
 * 文章投票
 */
class Vote extends Abstracts
{
    use Votable;
}
