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
