<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Topic;

/**
 * TopicModel Facade
 *
 * @method static array getList()
 * @method static void  decArticleCount($topicId, int $count = 1)
 * @method static void  incArticleCount($topicId, int $count = 1)
 * @method static void  decQuestionCount($topicId, int $count = 1)
 * @method static void  incQuestionCount($topicId, int $count = 1)
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
