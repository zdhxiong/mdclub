<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Facade\Library\Auth;

/**
 * 话题模型
 */
class Topic extends Abstracts
{
    public $table = 'topic';
    public $primaryKey = 'topic_id';
    protected $softDelete = true;

    public $columns = [
        'topic_id',
        'name',
        'cover',
        'description',
        'article_count',
        'question_count',
        'follower_count',
        'delete_time',
    ];

    public $allowOrderFields = [
        'topic_id',
        'follower_count',
    ];

    public $allowFilterFields = [
        'topic_id',
        'name',
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
            'article_count' => 0,
            'question_count' => 0,
            'follower_count' => 0,
        ])->all();
    }

    /**
     * 根据 url 参数获取话题列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this
            ->where($this->getWhereFromRequest())
            ->order($this->getOrderFromRequest(['topic_id' => 'ASC']))
            ->paginate();
    }

    /**
     * 减少指定话题的文章数量
     *
     * @param int $topicId
     * @param int $count
     */
    public function decArticleCount(int $topicId, int $count = 1): void
    {
        $this
            ->where('topic_id', $topicId)
            ->dec('article_count', $count)
            ->update();
    }

    /**
     * 减少指定话题的提问数量
     *
     * @param int $topicId
     * @param int $count
     */
    public function decQuestionCount(int $topicId, int $count = 1): void
    {
        $this
            ->where('topic_id', $topicId)
            ->dec('question_count', $count)
            ->update();
    }
}
