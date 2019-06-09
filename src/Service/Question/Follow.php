<?php

declare(strict_types=1);

namespace App\Service\Question;

use App\Traits\Followable;

/**
 * 关注提问
 */
class Follow extends Abstracts
{
    use Followable;
}
