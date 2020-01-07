<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Answer;

/**
 * AnswerModel Facade
 *
 * @method static array getByUserId(int $userId)
 * @method static array getByQuestionId(int $questionId)
 * @method static array getList()
 * @method static void  decCommentCount(int $answerId, int $count)
 */
class AnswerModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Answer::class;
    }
}
