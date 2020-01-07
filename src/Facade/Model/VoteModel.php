<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Vote;

/**
 * VoteModel Facade
 *
 * @method static void deleteByQuestionIds(array $questionIds)
 * @method static void deleteByAnswerIds(array $answerIds)
 * @method static void deleteByArticleIds(array $articleIds)
 * @method static void deleteByCommentIds(array $commentIds)
 * @method static void deleteByVoterIds(array $voterIds)
 */
class VoteModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Vote::class;
    }
}
