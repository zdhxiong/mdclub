<?php

declare(strict_types=1);

namespace App\Service\Question;

use App\Traits\Votable;

/**
 * 提问投票
 */
class Vote extends Abstracts
{
    use Votable;
}
