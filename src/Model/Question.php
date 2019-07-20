<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Observer\Question as QuestionObserver;

/**
 * 提问模型
 */
class Question extends Abstracts
{
    public $table = 'question';
    public $primaryKey = 'question_id';
    protected $timestamps = true;
    protected $softDelete = true;
    protected $observe = QuestionObserver::class;

    public $columns = [
        'question_id',
        'user_id',
        'title',
        'content_markdown',
        'content_rendered',
        'comment_count',
        'answer_count',
        'view_count',
        'follower_count',
        'vote_count',
        'create_time',
        'update_time',
        'delete_time',
    ];

    public $allowOrderFields = [
        'vote_count',
        'create_time',
        'update_time',
    ];

    public $allowFilterFields = [
        'question_id',
        'user_id',
        'topic_id', // topic_id 需要另外写逻辑
    ];

    /**
     * @inheritDoc
     */
    protected function beforeInsert(array $data): array
    {
        return collect($data)->union([
            'comment_count'    => 0,
            'answer_count'     => 0,
            'view_count'       => 0,
            'follower_count'   => 0,
            'vote_count'       => 0,
            'last_answer_time' => 0,
        ])->all();
    }

    /**
     * 根据 user_id 获取提问列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        return $this
            ->where('user_id', $userId)
            ->order($this->getOrderFromRequest(['update_time' => 'DESC']))
            ->paginate();
    }

    /**
     * 根据 topic_id 获取提问列表
     *
     * @param  int   $topicId
     * @return array
     */
    public function getByTopicId(int $topicId): array
    {

    }

    /**
     * 根据 url 参数获取未删除的提问列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this
            ->where($this->getWhereFromRequest())
            ->order($this->getOrderFromRequest(['update_time' => 'DESC']))
            ->paginate();
    }
}
