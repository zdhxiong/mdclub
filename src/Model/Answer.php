<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Facade\Library\Auth;

/**
 * 回答模型
 */
class Answer extends Abstracts
{
    public $table = 'answer';
    public $primaryKey = 'answer_id';
    protected $timestamps = true;
    protected $softDelete = true;

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

    public function __construct()
    {
        if (Auth::isManager()) {
            $this->allowOrderFields[] = 'delete_time';
        }

        parent::__construct();
    }

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
     * 根据指定字段的值获取回答列表
     *
     * @param string $field
     * @param int    $value
     *
     * @return array
     */
    private function getBy(string $field, int $value): array
    {
        return $this
            ->where($field, $value)
            ->order($this->getOrderFromRequest(['create_time' => 'DESC']))
            ->paginate();
    }

    /**
     * 根据 user_id 获取回答列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        return $this->getBy('user_id', $userId);
    }

    /**
     * 根据 question_id 获取回答列表
     *
     * @param  int   $questionId
     * @return array
     */
    public function getByQuestionId(int $questionId): array
    {
        return $this->getBy('question_id', $questionId);
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

    /**
     * 减少指定回答的评论数量
     *
     * @param int $answerId
     * @param int $count
     */
    public function decCommentCount(int $answerId, int $count): void
    {
        $this
            ->where('answer_id', $answerId)
            ->dec('comment_count', $count)
            ->update();
    }
}
