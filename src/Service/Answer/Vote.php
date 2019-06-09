<?php

declare(strict_types=1);

namespace App\Service\Answer;

use App\Traits\Votable;

/**
 * 回答投票
 */
class Vote extends Abstracts
{
    use Votable;
}
