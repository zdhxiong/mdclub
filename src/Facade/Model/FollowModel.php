<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Follow;

/**
 * FollowModel Facade
 *
 * @method static void deleteByUserIds(array $userIds)
 * @method static void deleteByQuestionIds(array $questionIds)
 * @method static void deleteByArticleIds(array $articleIds)
 * @method static void deleteByTopicIds(array $topicIds)
 * @method static void deleteByFollowerIds(array $followerIds)
 */
class FollowModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Follow::class;
    }
}
