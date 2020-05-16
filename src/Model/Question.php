<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Request;

/**
 * 提问模型
 */
class Question extends Abstracts
{
    public $table = 'question';
    public $primaryKey = 'question_id';
    protected $timestamps = true;
    protected $softDelete = true;

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
        'vote_up_count',
        'vote_down_count',
        'last_answer_time',
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
            'comment_count'    => 0,
            'answer_count'     => 0,
            'view_count'       => 0,
            'follower_count'   => 0,
            'vote_count'       => 0,
            'vote_up_count'    => 0,
            'vote_down_count'  => 0,
            'last_answer_time' => 0,
        ])->all();
    }

    /**
     * @inheritDoc
     */
    public function getWhereFromRequest(array $defaultFilter = []): array
    {
        $where = parent::getWhereFromRequest($defaultFilter);

        if (isset($where['topic_id'])) {
            $this->join(['[><]topicable' => ['question_id' => 'topicable_id']]);

            $where['topicable.topic_id'] = $where['topic_id'];
            $where['topicable.topicable_type'] = 'question';
            unset($where['topic_id']);
        }

        if (isset($where['user_id'])) {
            $where['question.user_id'] = $where['user_id'];
            unset($where['user_id']);
        }

        if (isset($where['question_id'])) {
            $where['question.question_id'] = $where['question_id'];
            unset($where['question_id']);
        }

        return $where;
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
        return $this
            ->join(['[><]topicable' => ['question_id' => 'topicable_id']])
            ->where('topicable.topicable_type', 'question')
            ->where('topicable.topic_id', $topicId)
            ->order($this->getOrderFromRequest(['update_time' => 'DESC']))
            ->paginate();
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

    /**
     * 增加指定提问的回答数量
     *
     * @param int $questionId
     * @param int $count
     */
    public function incAnswerCount(int $questionId, int $count = 1): void
    {
        $this
            ->where('question_id', $questionId)
            ->inc('answer_count', $count)
            ->set('last_answer_time', Request::time())
            ->update();
    }

    /**
     * 减少指定提问的回答数量
     *
     * @param int $questionId
     * @param int $count
     */
    public function decAnswerCount(int $questionId, int $count = 1): void
    {
        $this
            ->where('question_id', $questionId)
            ->dec('answer_count', $count)
            ->update();
    }

    /**
     * 减少指定提问的评论数量
     *
     * @param int $questionId
     * @param int $count
     */
    public function decCommentCount(int $questionId, int $count = 1): void
    {
        $this
            ->where('question_id', $questionId)
            ->dec('comment_count', $count)
            ->update();
    }
}
