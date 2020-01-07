<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Topicable;

/**
 * TopicableModel Facade
 *
 * @method static void deleteByQuestionIds(array $questionIds)
 * @method static void deleteByArticleIds(array $articleIds)
 * @method static void deleteByTopicIds(array $topicIds)
 */
class TopicableModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Topicable::class;
    }
}
