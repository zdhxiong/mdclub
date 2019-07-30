<?php

declare(strict_types=1);

namespace MDClub\Service\User;

use MDClub\Traits\Followable;

/**
 * 用户关注
 */
class Follow extends Abstracts
{
    use Followable;
}
