<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Question;

/**
 * QuestionModel Facade
 *
 * @method static array getByUserId(int $userId)
 * @method static array getByTopicId(int $topicId)
 * @method static array getList()
 * @method static void  incAnswerCount(int $questionId, int $count = 1)
 * @method static void  decAnswerCount(int $questionId, int $count = 1)
 * @method static void  decCommentCount(int $questionId, int $count = 1)
 */
class QuestionModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Question::class;
    }
}
