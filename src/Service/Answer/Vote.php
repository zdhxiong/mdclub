<?php

declare(strict_types=1);

namespace MDClub\Service\Answer;

use MDClub\Traits\Votable;

/**
 * 回答投票
 */
class Vote extends Abstracts
{
    use Votable;
}
