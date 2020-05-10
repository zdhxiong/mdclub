<?php

declare(strict_types=1);

namespace MDClub\Model;

/**
 * 投票模型
 */
class Vote extends Abstracts
{
    public $table = 'vote';
    protected $timestamps = true;

    protected const UPDATE_TIME = false;

    public $columns = [
        'user_id',
        'votable_id',
        'votable_type',
        'type',
        'create_time',
    ];

    /**
     * 根据特定类型的ID删除投票关系
     *
     * @param string $type
     * @param array  $ids
     */
    protected function deleteByIds(string $type, array $ids): void
    {
        if (!$ids) {
            return;
        }

        $this
            ->where('votable_type', $type)
            ->where('votable_id', $ids)
            ->delete();
    }

    /**
     * 根据提问ID删除投票关系
     *
     * @param array $questionIds
     */
    public function deleteByQuestionIds(array $questionIds): void
    {
        $this->deleteByIds('question', $questionIds);
    }

    /**
     * 根据回答ID删除投票关系
     *
     * @param array $answerIds
     */
    public function deleteByAnswerIds(array $answerIds): void
    {
        $this->deleteByIds('answer', $answerIds);
    }

    /**
     * 根据文章ID删除投票关系
     *
     * @param array $articleIds
     */
    public function deleteByArticleIds(array $articleIds): void
    {
        $this->deleteByIds('article', $articleIds);
    }

    /**
     * 根据评论ID删除投票关系
     *
     * @param array $commentIds
     */
    public function deleteByCommentIds(array $commentIds): void
    {
        $this->deleteByIds('comment', $commentIds);
    }

    /**
     * 根据投票者ID删除投票关系
     *
     * @param array $voterIds
     */
    public function deleteByVoterIds(array $voterIds): void
    {
        $this->where('user_id', $voterIds)->delete();
    }
}
