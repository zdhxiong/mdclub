<?php

declare(strict_types=1);

namespace MDClub\Model;

use MDClub\Facade\Library\Auth;

/**
 * 评论模型
 */
class Comment extends Abstracts
{
    public $table = 'comment';
    public $primaryKey = 'comment_id';
    protected $timestamps = true;
    protected $softDelete = true;

    public $columns = [
        'comment_id',
        'commentable_id',
        'commentable_type',
        'user_id',
        'content',
        'reply_count',
        'vote_count',
        'vote_up_count',
        'vote_down_count',
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
            'vote_count'      => 0,
            'vote_up_count'   => 0,
            'vote_down_count' => 0,
            'reply_count'     => 0,
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
     * 根据关联ID删除评论
     *
     * @param string $type
     * @param array  $ids
     */
    protected function deleteByIds(string $type, array $ids): void
    {
        if (!$ids) {
            return;
        }

        $this
            ->force()
            ->where('commentable_type', $type)
            ->where('commentable_id', $ids)
            ->delete();
    }

    /**
     * 根据回答ID删除评论
     *
     * @param array $answerIds
     */
    public function deleteByAnswerIds(array $answerIds): void
    {
        $this->deleteByIds('answer', $answerIds);
    }

    /**
     * 根据文章ID删除评论
     *
     * @param array $articleIds
     */
    public function deleteByArticleIds(array $articleIds): void
    {
        $this->deleteByIds('article', $articleIds);
    }

    /**
     * 根据提问ID删除评论
     *
     * @param array $questionIds
     */
    public function deleteByQuestionIds(array $questionIds): void
    {
        $this->deleteByIds('question', $questionIds);
    }
}
