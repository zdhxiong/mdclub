<?php

declare(strict_types=1);

namespace MDClub\Service\Comment;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;

/**
 * 删除评论
 *
 * 管理员可删除所有评论，普通用户是否可删除自己的评论由配置项设置
 *
 * 涉及的配置项：
 * comment_can_delete         是否可编辑（总开关）
 * comment_can_delete_before  在发表后的多少秒内可删除（0表示不作时间限制）
 */
class Delete extends Abstracts
{
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
            throw new ApiException(ApiError::COMMENT_CANT_DELETE_NOT_AUTHOR);
        }

        $this->commentModel->delete($commentId);

        // 该评论目标对象的 comment_count - 1
        $this->{"${commentInfo['commentable_type']}Model"}
            ->where("${commentInfo['commentable_type']}_id", $commentInfo['commentable_id'])
            ->dec('comment_count')
            ->update();
    }

    /**
     * 批量删除评论
     *
     * @param array $commentIds
     */
    public function deleteMultiple(array $commentIds): void
    {
        if (!$commentIds) {
            return;
        }

        $comments = $this->commentModel
            ->field(['comment_id', 'user_id', 'commentable_id', 'commentable_type'])
            ->select($commentIds);

        if (!$comments) {
            return;
        }

        $commentIds = array_column($comments, 'comment_id');
        $this->commentModel->delete($commentIds);

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
                    ->where($type . '_id', $targetId)
                    ->dec('comment_count', $count)
                    ->update();
            }
        }
    }

    /**
     * 恢复评论
     *
     * @param int $commentId
     */
    public function restore(int $commentId): void
    {

    }

    /**
     * 批量恢复评论
     *
     * @param array $commentIds
     */
    public function restoreMultiple(array $commentIds): void
    {

    }

    /**
     * 硬删除评论
     *
     * @param int $commentId
     */
    public function destroy(int $commentId): void
    {

    }

    /**
     * 批量硬删除评论
     *
     * @param array $commentIds
     */
    public function destroyMultiple(array $commentIds): void
    {

    }
}
