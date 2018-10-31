<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\FollowAbstracts;

/**
 * 话题关注
 *
 * Class TopicFollowService
 * @package App\Service
 */
class TopicFollowService extends FollowAbstracts
{
    /**
     * 关注类型
     *
     * @var string
     */
    protected $followableType = 'topic';
}
