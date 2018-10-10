<?php

declare(strict_types=1);

namespace App\Service;

/**
 * 用户关注
 *
 * Class UserFollowService
 * @package App\Service
 */
class UserFollowService extends FollowableService
{
    /**
     * 关注类型
     *
     * @var string
     */
    protected $followableType = 'user';
}
