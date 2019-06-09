<?php

declare(strict_types=1);

namespace App\Service\Comment;

use App\Traits\Getable;
use Tightenco\Collect\Support\Collection;

/**
 * 获取评论
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return $this->roleService->managerId()
            ? []
            : ['delete_time'];
    }

    /**
     * 获取允许排序的字段
     *
     * @return array
     */
    public function getAllowOrderFields(): array
    {
        return ['vote_count', 'create_time'];
    }

    /**
     * 允许过滤的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return ['comment_id', 'commentable_id', 'commentable_type', 'user_id'];
    }

    /**
     * 对结果中的内容进行处理
     *
     * @param  array $comments
     * @return array
     */
    public function addFormatted(array $comments): array
    {
        return $comments;
    }

    /**
     * 为结果添加相关信息
     *
     * @param  array $comments
     * @return array
     */
    public function addRelationship(array $comments): array
    {
        $commentIds = array_unique(array_column($comments, 'comment_id'));
        $userIds = array_unique(array_column($comments, 'user_id'));

        $votings = $this->voteService->getVotingInRelationship($commentIds, 'comment');
        $users = $this->userService->getInRelationship($userIds);

        // 合并数据
        foreach ($comments as &$comment) {
            $comment['relationship'] = [
                'user'   => $users[$comment['user_id']],
                'voting' => $votings[$comment['comment_id']],
            ];
        }

        unset($comment);

        return $comments;
    }

    /**
     * 执行查询列表操作
     *
     * @return array|Collection
     */
    protected function doGetList()
    {
        $this->beforeGet();

        $result = $this->model->paginate();
        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 根据 user_id 获取评论列表
     *
     * @param  int              $userId
     * @return array|Collection
     */
    public function getByUserId(int $userId)
    {
        $this->userGetService->hasOrFail($userId);

        $this->model
            ->where('user_id', $userId)
            ->order($this->getOrder(['create_time' => 'DESC']));

        return $this->doGetList();
    }

    /**
     * 获取已删除的评论列表
     *
     * @return array|Collection
     */
    public function getDeleted()
    {
        $defaultOrder = ['delete_time' => 'DESC'];
        $allowOrderFields = collect($this->getAllowOrderFields())->push('delete_time')->unique()->all();
        $order = $this->getOrder($defaultOrder, $allowOrderFields);

        $this->model
            ->onlyTrashed()
            ->where($this->getWhere())
            ->order($order);

        return $this->doGetList();
    }

    /**
     * 获取评论列表
     *
     * @return array|Collection
     */
    public function getList()
    {
        $this->model
            ->where($this->getWhere())
            ->order($this->getOrder(['create_time' => 'DESC']));

        return $this->doGetList();
    }

    /**
     * 获取 relationship 中使用的 comment
     *
     * @param  array $commentIds
     * @return array
     */
    public function getInRelationship(array $commentIds): array
    {
        $comments = array_combine($commentIds, array_fill(0, count($commentIds), []));

        return $this->model
            ->field(['comment_id', 'content', 'create_time', 'update_time'])
            ->fetchCollection()
            ->select($commentIds)
            ->keyBy('comment_id')
            ->map(static function ($item) {
                $item['content_summary'] = mb_substr($item['content'], 0, 80);
            })
            ->union($comments)
            ->all();
    }
}
