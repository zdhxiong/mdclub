<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Observer\Topic as TopicObserver;

/**
 * 话题模型
 */
class Topic extends Abstracts
{
    public $table = 'topic';
    public $primaryKey = 'topic_id';
    protected $softDelete = true;
    protected $observe = TopicObserver::class;

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
     * 根据 url 参数获取已删除的话题列表
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
}
