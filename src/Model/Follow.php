<?php

declare(strict_types=1);

namespace MDClub\Model;

/**
 * 关注模型
 */
class Follow extends Abstracts
{
    public $table = 'follow';
    protected $timestamps = true;

    protected const UPDATE_TIME = false; // 不维护 update_time 字段

    public $columns = [
        'user_id',
        'followable_id',
        'followable_type',
        'create_time',
    ];

    /**
     * 根据指定类型的ID删除关注关系
     *
     * @param string $type
     * @param array  $ids
     */
    protected function deleteByIds(string $type, array $ids): void
    {
        $this
            ->where('followable_type', $type)
            ->where('followable_id', $ids)
            ->delete();
    }

    /**
     * 根据被关注者ID删除关注关系
     *
     * @param array $userIds
     */
    public function deleteByUserIds(array $userIds): void
    {
        $this->deleteByIds('user', $userIds);
    }

    /**
     * 根据提问ID删除关注关系
     *
     * @param array $questionIds
     */
    public function deleteByQuestionIds(array $questionIds): void
    {
        $this->deleteByIds('question', $questionIds);
    }

    /**
     * 根据文章ID删除关注关系
     *
     * @param array $articleIds
     */
    public function deleteByArticleIds(array $articleIds): void
    {
        $this->deleteByIds('article', $articleIds);
    }

    /**
     * 根据话题ID删除关注关系
     *
     * @param array $topicIds
     */
    public function deleteByTopicIds(array $topicIds): void
    {
        $this->deleteByIds('topic', $topicIds);
    }

    /**
     * 根据关注者ID删除关注关系
     *
     * @param array $followerIds
     */
    public function deleteByFollowerIds(array $followerIds): void
    {
        $this->where('user_id', $followerIds)->delete();
    }
}
