<?php

declare(strict_types=1);

namespace App\Service\Topic;

use App\Traits\Followable;

/**
 * 话题关注
 */
class Follow extends Abstracts
{
    use Followable;
}
