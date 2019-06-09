<?php

declare(strict_types=1);

namespace App\Service\Article;

use App\Traits\Followable;

/**
 * 文章关注
 */
class Follow extends Abstracts
{
    use Followable;
}
