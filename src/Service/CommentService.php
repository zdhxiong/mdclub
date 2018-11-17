<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Traits\baseTraits;
use App\Traits\VotableTraits;

/**
 * 评论
 *
 * @property-read \App\Model\CommentModel      currentModel
 *
 * Class CommentService
 * @package App\Service
 */
class CommentService extends ServiceAbstracts
{
    use baseTraits, VotableTraits;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return ['delete_time'];
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
     * 获取评论列表
     *
     * @param  bool  $withRelationship
     * @return array
     */
    public function getList(bool $withRelationship = false): array
    {
        $list = $this->commentModel
            ->where($this->getWhere())
            ->order($this->getOrder(['create_time' => 'DESC']))
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        foreach ($list['data'] as &$item) {
            $item = $this->handle($item);
        }

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 根据用户ID获取问题列表
     *
     * @param  int   $userId
     * @param  bool  $withRelationship
     * @return array
     */
    public function getListByUserId(int $userId, bool $withRelationship = false): array
    {
        $this->userService->hasOrFail($userId);

        $list = $this->commentModel
            ->where(['user_id' => $userId])
            ->order($this->getOrder(['create_time' => 'DESC']))
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        foreach ($list['data'] as &$item) {
            $item = $this->handle($item);
        }

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 对数据库中取出的评论数据进行处理
     *
     * @param  array $commentInfo
     * @return array
     */
    public function handle($commentInfo): array
    {
        return $commentInfo;
    }

    /**
     * 为评论添加 relationship 字段
     * {
     *     user: {},
     *     voting: up、down、''
     * }
     *
     * @param  array $comments
     * @return array
     */
    public function addRelationship(array $comments): array
    {
        if (!$comments) {
            return $comments;
        }

        if (!$isArray = is_array(current($comments))) {
            $comments = [$comments];
        }

        $commentIds = array_unique(array_column($comments, 'comment_id'));
        $userIds = array_unique(array_column($comments, 'user_id'));

        $votings = $this->voteService->getVotingInRelationship($commentIds, 'comment');
        $users = $this->userService->getUsersInRelationship($userIds);

        // 合并数据
        foreach ($comments as &$comment) {
            $comment['relationship'] = [
                'user'   => $users[$comment['user_id']],
                'voting' => $votings[$comment['comment_id']],
            ];
        }

        if ($isArray) {
            return $comments;
        }

        return $comments[0];
    }
}
