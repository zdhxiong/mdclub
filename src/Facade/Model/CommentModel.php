<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Comment;

/**
 * CommentModel Facade
 *
 * @method static array getByUserId(int $userId)
 * @method static array getByCommentableId(string $commentableType, int $commentableId)
 * @method static array getList()
 * @method static void  deleteByAnswerIds(array $answerIds)
 * @method static void  deleteByArticleIds(array $articleIds)
 * @method static void  deleteByQuestionIds(array $questionIds)
 */
class CommentModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Comment::class;
    }
}
