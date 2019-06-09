<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Traits\Followable;

/**
 * 用户关注
 */
class Follow extends Abstracts
{
    use Followable;
}
