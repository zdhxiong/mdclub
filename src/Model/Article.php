<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Observer\Article as ArticleObserver;

/**
 * 文章模型
 */
class Article extends Abstracts
{
    public $table = 'article';
    public $primaryKey = 'article_id';
    protected $timestamps = true;
    protected $softDelete = true;
    protected $observe = ArticleObserver::class;

    public $columns = [
        'article_id',
        'user_id',
        'title',
        'content_markdown',
        'content_rendered',
        'comment_count',
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
        'article_id',
        'user_id',
        'topic_id', // topic_id 需要另外写逻辑
    ];

    /**
     * @inheritDoc
     */
    protected function beforeInsert(array $data): array
    {
        return collect($data)->union([
            'comment_count'  => 0,
            'view_count'     => 0,
            'follower_count' => 0,
            'vote_count'     => 0,
        ])->all();
    }

    /**
     * @inheritDoc
     */
    public function getWhereFromRequest(array $defaultFilter = [], array $allowFilterFields = null): array
    {
        $where = parent::getWhereFromRequest($defaultFilter, $allowFilterFields);

        if (isset($where['topic_id'])) {
            $this->join(['[><]topicable' => ['article_id' => 'topicable_id']]);

            $where['topicable.topic_id'] = $where['topic_id'];
            $where['topicable.topicable_type'] = 'article';
            unset($where['topic_id']);
        }

        if (isset($where['user_id'])) {
            $where['article.user_id'] = $where['user_id'];
            unset($where['user_id']);
        }

        if (isset($where['article_id'])) {
            $where['article.article_id'] = $where['article_id'];
            unset($where['article_id']);
        }

        return $where;
    }

    /**
     * 根据 user_id 获取文章列表
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
     * 根据 topic_id 获取文章列表
     *
     * @param  int   $topicId
     * @return array
     */
    public function getByTopicId(int $topicId): array
    {
        return $this
            ->join(['[><]topicable' => ['article_id' => 'topicable_id']])
            ->where('topicable.topicable_type', 'article')
            ->where('topicable.topic_id', $topicId)
            ->order($this->getOrderFromRequest(['create_time' => 'DESC']))
            ->paginate();
    }

    /**
     * 根据 url 参数获取未删除的文章列表
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
