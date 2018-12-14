<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;
use App\Traits\BaseTraits;
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
    use BaseTraits, VotableTraits;

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

        $list['data'] = $this->handle($list['data']);

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 根据用户ID获取提问列表
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

        $list['data'] = $this->handle($list['data']);

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * @param  int    $commentId 评论ID
     * @param  string $content   评论内容
     */
    public function update(int $commentId, string $content = null): void
    {
        $userId = $this->roleService->userIdOrFail();
        $commentInfo = $this->commentModel->get($commentId);

        if (!$commentInfo) {
            throw new ApiException(ErrorConstant::COMMENT_NOT_FOUND);
        }

        if ($commentInfo['user_id'] != $userId && !$this->roleService->managerId()) {
            throw new ApiException(ErrorConstant::COMMENT_ONLY_AUTHOR_CAN_EDIT);
        }

        $errors = [];

        if ($content) {
            $content = strip_tags($content);
        }

        // 验证内容
        if (!is_null($content)) {
            if (!$content) {
                $errors['content'] = '评论内容不能为空';
            } elseif (!ValidatorHelper::isMax($content, 1000)) {
                $errors['content'] = '评论内容不能超过 1000 个字符';
            }

            if ($errors) {
                throw new ValidationException($errors);
            } else {
                $this->commentModel
                    ->where(['comment_id' => $commentId])
                    ->update(['content' => $content]);
            }
        }
    }

    /**
     * 删除评论
     *
     * @param int $commentId
     */
    public function delete(int $commentId): void
    {
        $userId = $this->roleService->userIdOrFail();
        $commentInfo = $this->commentModel
            ->field(['user_id', 'commentable_id', 'commentable_type'])
            ->get($commentId);

        if (!$commentInfo) {
            return;
        }

        if ($commentInfo['user_id'] != $userId && !$this->roleService->managerId()) {
            throw new ApiException(ErrorConstant::COMMENT_ONLY_AUTHOR_CAN_DELETE);
        }

        $this->commentModel->delete($commentId);

        // 该评论目标对象的 comment_count - 1
        $this->{$commentInfo['commentable_type'] . 'Model'}
            ->where([$commentInfo['commentable_type'] . '_id' => $commentInfo['commentable_id']])
            ->update(['comment_count[-]' => 1]);
    }

    /**
     * 批量删除评论
     *
     * @param array $commentIds
     */
    public function batchDelete(array $commentIds): void
    {
        $comments = $this->commentModel
            ->field(['comment_id', 'user_id', 'commentable_id', 'commentable_type'])
            ->where(['comment_id' => $commentIds])
            ->select();

        if (!$comments) {
            return;
        }

        $commentIds = array_column($comments, 'comment_id');
        $this->commentModel->where(['comment_id' => $commentIds])->delete();

        $targets = [];

        // 这些评论的目标对象的 comment_count - 1
        foreach ($comments as $comment) {
            if (!isset($targets[$comment['commentable_type']])) {
                $targets[$comment['commentable_type']] = [];
            }

            isset($targets[$comment['commentable_type']][$comment['commentable_id']])
                ? $targets[$comment['commentable_type']][$comment['commentable_id']] += 1
                : $targets[$comment['commentable_type']][$comment['commentable_id']] = 1;
        }

        foreach ($targets as $type => $target) {
            foreach ($target as $targetId => $count) {
                $this->{$type . 'Model'}
                    ->where([$type . '_id' => $targetId])
                    ->update(['comment_count[-]' => $count]);
            }
        }
    }

    /**
     * 对数据库中取出的评论数据进行处理
     *
     * @param  array $comments 评论信息，或多个评论组成的数组
     * @return array
     */
    public function handle($comments): array
    {
        if (!$comments) {
            return $comments;
        }

        if (!$isArray = is_array(current($comments))) {
            $comments = [$comments];
        }

        foreach ($comments as &$comment) {
            // todo 处理评论
        }

        if ($isArray) {
            return $comments;
        }

        return $comments[0];
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

        $commentsTmp = $this->commentModel
            ->field(['comment_id', 'content', 'create_time', 'update_time'])
            ->select($commentIds);

        foreach ($commentsTmp as $item) {
            $comments[$item['comment_id']] = [
                'comment_id'      => $item['comment_id'],
                'content_summary' => mb_substr($item['content'], 0, 80),
                'create_time'     => $item['create_time'],
                'update_time'     => $item['update_time'],
            ];
        }

        return $comments;
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
        $users = $this->userService->getInRelationship($userIds);

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
