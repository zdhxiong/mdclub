<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Topic;

/**
 * TopicModel Facade
 *
 * @method static array getList()
 * @method static void  decArticleCount(int $topicId, int $count = 1)
 * @method static void  decQuestionCount(int $topicId, int $count = 1)
 */
class TopicModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Topic::class;
    }
}
