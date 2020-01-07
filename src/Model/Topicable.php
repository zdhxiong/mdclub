<?php

declare(strict_types=1);

namespace MDClub\Model;

/**
 * 话题关系模型
 */
class Topicable extends Abstracts
{
    public $table = 'topicable';
    protected $timestamps = true;

    protected const UPDATE_TIME = false; // 不维护 update_time 字段

    public $columns = [
        'topic_id',
        'topicable_id',
        'topicable_type',
        'create_time',
    ];

    /**
     * 根据提问ID删除话题关系
     *
     * @param array $questionIds
     */
    public function deleteByQuestionIds(array $questionIds): void
    {
        $this
            ->where('topicable_type', 'question')
            ->where('topicable_id', $questionIds)
            ->delete();
    }

    /**
     * 根据文章ID删除话题关系
     *
     * @param array $articleIds
     */
    public function deleteByArticleIds(array $articleIds): void
    {
        $this
            ->where('topicable_type', 'article')
            ->where('topicable_id', $articleIds)
            ->delete();
    }

    /**
     * 根据话题ID删除话题关系
     *
     * @param array $topicIds
     */
    public function deleteByTopicIds(array $topicIds): void
    {
        $this->where('topic_id', $topicIds)->delete();
    }
}
