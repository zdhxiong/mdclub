<?php

declare(strict_types=1);

namespace MDClub\Service\Question;

use MDClub\Traits\Votable;

/**
 * 提问投票
 */
class Vote extends Abstracts
{
    use Votable;
}
