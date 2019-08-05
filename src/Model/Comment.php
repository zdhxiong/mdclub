<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Observer\Comment as CommentObserver;

/**
 * 评论模型
 */
class Comment extends Abstracts
{
    public $table = 'comment';
    public $primaryKey = 'comment_id';
    protected $timestamps = true;
    protected $softDelete = true;
    protected $observe = CommentObserver::class;

    public $columns = [
        'comment_id',
        'commentable_id',
        'commentable_type',
        'user_id',
        'content',
        'vote_count',
        'create_time',
        'update_time',
        'delete_time',
    ];

    public $allowOrderFields = [
        'vote_count',
        'create_time'
    ];

    public $allowFilterFields = [
        'comment_id',
        'commentable_id',
        'commentable_type',
        'user_id'
    ];

    /**
     * @inheritDoc
     */
    protected function beforeInsert(array $data): array
    {
        return collect($data)->union([
            'vote_count' => 0,
        ])->all();
    }

    /**
     * 根据 user_id 获取评论列表
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
     * 根据 commentable_type, commentable_id 获取评论列表
     *
     * @param  string $commentableType
     * @param  int    $commentableId
     * @return array
     */
    public function getByCommentableId(string $commentableType, int $commentableId): array
    {
        return $this
            ->where('commentable_type', $commentableType)
            ->where('commentable_id', $commentableId)
            ->order($this->getOrderFromRequest(['create_time' => 'DESC']))
            ->paginate();
    }

    /**
     * 根据 url 参数获取未删除的评论列表
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
     * 根据 url 参数获取回收站中的评论列表
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
