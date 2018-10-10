<?php

declare(strict_types=1);

namespace App\Service;

/**
 * 问题关注
 *
 * Class QuestionFollowService
 * @package App\Service
 */
class QuestionFollowService extends FollowableService
{
    /**
     * 关注类型
     *
     * @var string
     */
    protected $followableType = 'question';
}
