<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Report;

/**
 * ReportModel Facade
 *
 * @method static array getList()
 * @method static void  deleteByQuestionIds(array $questionIds)
 * @method static void  deleteByArticleIds(array $articleIds)
 * @method static void  deleteByAnswerIds(array $answerIds)
 * @method static void  deleteByCommentIds(array $commentIds)
 * @method static void  deleteByUserIds(array $userIds)
 * @method static void  deleteByReporterIds(array $reporterIds)
 */
class ReportModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Report::class;
    }
}
