<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\FollowableAbstracts;

/**
 * 用户关注
 *
 * Class UserFollowService
 * @package App\Service
 */
class UserFollowService extends FollowableAbstracts
{
    /**
     * 关注类型
     *
     * @var string
     */
    protected $followableType = 'user';
}
