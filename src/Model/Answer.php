<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Observer\Answer as AnswerObserver;

/**
 * 回答模型
 */
class Answer extends Abstracts
{
    public $table = 'answer';
    public $primaryKey = 'answer_id';
    protected $timestamps = true;
    protected $softDelete = true;
    protected $observe = AnswerObserver::class;

    public $columns = [
        'answer_id',
        'question_id',
        'user_id',
        'content_markdown',
        'content_rendered',
        'comment_count',
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
        'answer_id',
        'question_id',
        'user_id',
    ];

    /**
     * @inheritDoc
     */
    protected function beforeInsert(array $data): array
    {
        return collect($data)->union([
            'comment_count' => 0,
            'vote_count'    => 0,
        ])->all();
    }

    /**
     * 根据 user_id 获取回答列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        return $this
            ->where('user_id', $userId)
            ->order($this->getOrderFromRequest(['create_time' => 'DESC']))
            ->paginate();
    }

    /**
     * 根据 question_id 获取回答列表
     *
     * @param  int   $questionId
     * @return array
     */
    public function getByQuestionId(int $questionId): array
    {
        return $this
            ->where('question_id', $questionId)
            ->order($this->getOrderFromRequest(['create_time' => 'DESC']))
            ->paginate();
    }

    /**
     * 获取 url 参数获取已删除的回答列表
     *
     * @return array
     */
    public function getDeleted(): array
    {
        $defaultOrder = ['delete_time' => 'DESC'];
        $allowOrderFields = collect($this->allowOrderFields)->push('delete_time')->unique()->all();
        $order = $this->getOrderFromRequest($defaultOrder, $allowOrderFields);

        return $this
            ->onlyTrashed()
            ->where($this->getWhereFromRequest())
            ->order($order)
            ->paginate();
    }

    /**
     * 根据 url 参数获取未删除的回答列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this
            ->where($this->getWhereFromRequest())
            ->order($this->getOrderFromRequest(['create_time' => 'DESC']))
            ->paginate();
    }
}
