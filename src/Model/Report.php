<?php

declare(strict_types=1);

namespace MDClub\Model;

use Medoo\Medoo;

/**
 * 举报模型
 */
class Report extends Abstracts
{
    public $table = 'report';
    public $primaryKey = 'report_id';
    protected $timestamps = true;

    protected const UPDATE_TIME = false;

    public $columns = [
        'report_id',
        'reportable_id',
        'reportable_type',
        'user_id',
        'reason',
        'create_time',
    ];

    public $allowFilterFields = [
        'reportable_type'
    ];

    /**
     * 获取被举报的内容列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this
            ->where($this->getWhereFromRequest())
            ->field([
                'reporter_count' => Medoo::raw('COUNT(<report_id>)'),
                'reportable_id',
                'reportable_type',
            ])
            ->order('reporter_count', 'DESC')
            ->group([
                'reportable_id',
                'reportable_type',
            ])
            ->paginate();
    }

    /**
     * 根据指定类型的ID删除举报
     *
     * @param string $type question, article, answer, comment, user
     * @param array  $ids
     */
    protected function deleteByIds(string $type, array $ids): void
    {
        if (!$ids) {
            return;
        }

        $this
            ->where('reportable_type', $type)
            ->where('reportable_id', $ids)
            ->delete();
    }

    /**
     * 根据提问ID删除举报
     *
     * @param array $questionIds
     */
    public function deleteByQuestionIds(array $questionIds): void
    {
        $this->deleteByIds('question', $questionIds);
    }

    /**
     * 根据文章ID删除举报
     *
     * @param array $articleIds
     */
    public function deleteByArticleIds(array $articleIds): void
    {
        $this->deleteByIds('article', $articleIds);
    }

    /**
     * 根据回答ID删除举报
     *
     * @param array $answerIds
     */
    public function deleteByAnswerIds(array $answerIds): void
    {
        $this->deleteByIds('answer', $answerIds);
    }

    /**
     * 根据评论ID删除举报
     *
     * @param array $commentIds
     */
    public function deleteByCommentIds(array $commentIds): void
    {
        $this->deleteByIds('comment', $commentIds);
    }

    /**
     * 根据被举报者用户ID删除举报
     *
     * @param array $userIds
     */
    public function deleteByUserIds(array $userIds): void
    {
        $this->deleteByIds('user', $userIds);
    }

    /**
     * 根据举报者ID删除举报
     *
     * @param array $reporterIds
     */
    public function deleteByReporterIds(array $reporterIds): void
    {
        $this->where('user_id', $reporterIds)->delete();
    }
}
