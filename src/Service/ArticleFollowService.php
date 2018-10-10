<?php

declare(strict_types=1);

namespace App\Service;

/**
 * 文章关注
 *
 * Class ArticleFollowService
 * @package App\Service
 */
class ArticleFollowService extends FollowableService
{
    /**
     * 关注类型
     *
     * @var string
     */
    protected $followableType = 'article';
}
