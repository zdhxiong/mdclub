<?php

declare(strict_types=1);

namespace MDClub\Service\Comment;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Helper\Request;

/**
 * 删除评论
 *
 * 管理员可删除所有评论，可指定软删除和硬删除。
 * 普通用户是否可删除自己的评论由配置项设置，且只可硬删除。
 *
 * 涉及的配置项：
 * comment_can_delete         是否可编辑（总开关）
 * comment_can_delete_before  在发表后的多少秒内可删除（0表示不作时间限制）
 */
class Delete extends Abstracts
{
    /**
     * 软删除评论
     *
     * @param int $commentId
     */
    public function delete(int $commentId): void
    {
        $this->model->delete($commentId);
    }

    /**
     * 批量软删除评论
     *
     * @param array $commentIds
     */
    public function deleteMultiple(array $commentIds): void
    {
        if (!$commentIds) {
            return;
        }

        $this->model->delete($commentIds);
    }

    /**
     * 恢复评论
     *
     * @param  int $commentId
     * @return int            恢复的数量
     */
    public function restore(int $commentId): int
    {
        return $this->model->restore($commentId);
    }

    /**
     * 批量恢复评论
     *
     * @param array $commentIds
     */
    public function restoreMultiple(array $commentIds): void
    {
        if (!$commentIds) {
            return;
        }

        $this->model->restore($commentIds);
    }

    /**
     * 执行硬删除
     *
     * @param array $comments
     */
    protected function doDestroy(array $comments): void
    {
        $commentIds = array_column($comments, 'comment_id');
        $this->model->force()->delete($commentIds);

        // 这些评论的目标对象的 comment_count - 1
        $targets = [];
        foreach ($comments as $comment) {
            $type = $comment['commentable_type'];
            $id = $comment['commentable_id'];

            if (!isset($targets[$type])) {
                $targets[$type] = [];
            }

            isset($targets[$type][$id])
                ? $targets[$type][$id] += 1
                : $targets[$type][$id] = 1;
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
     * 检查普通用户是否有删除权限
     *
     * @param array $comment 评论信息
     */
    protected function checkPermission(array $comment): void
    {
        $userId = $this->auth->userId();

        if ($comment['user_id'] !== $userId) {
            throw new ApiException(ApiError::COMMENT_CANT_DELETE_NOT_AUTHOR);
        }

        $canDelete = $this->option->comment_can_delete;
        $canDeleteBefore = (int) $this->option->comment_can_delete_before;
        $requestTime = Request::time($this->request);

        if (!$canDelete) {
            throw new ApiException(ApiError::COMMENT_CANT_DELETE);
        }

        if ($canDeleteBefore && $comment['create_time'] + $canDeleteBefore > $requestTime) {
            throw new ApiException(ApiError::COMMENT_CANT_DELETE_TIMEOUT);
        }
    }

    /**
     * 硬删除评论
     *
     * NOTE: 普通用户的删除只调用该方法，不涉及这个类的其他方法
     *
     * @param int   $commentId
     * @param bool  $force     是否强制删除。为 true 时，无论话题是否在回收站中，都直接删除；为 false 时，只删除在回收站中的话题。
     */
    public function destroy(int $commentId, bool $force = false): void
    {
        if ($force) {
            $this->model->force();
        } else {
            $this->model->onlyTrashed();
        }

        $comment = $this->model
            ->field(['comment_id', 'user_id', 'commentable_id', 'commentable_type'])
            ->get($commentId);

        if ($this->auth->isNotManager()) {
            // 普通用户删除不存在的评论时，要抛出错误码
            if (!$comment) {
                throw new ApiException(ApiError::COMMENT_NOT_FOUND);
            }

            $this->checkPermission($comment);
        }

        if ($comment) {
            $this->doDestroy([$comment]);
        }
    }

    /**
     * 批量硬删除评论
     *
     * @param array $commentIds
     * @param bool  $force      是否强制删除。为 true 时，无论话题是否在回收站中，都直接删除；为 false 时，只删除在回收站中的话题。
     */
    public function destroyMultiple(array $commentIds, bool $force = false): void
    {
        if (!$commentIds) {
            return;
        }

        if ($force) {
            $this->model->force();
        } else {
            $this->model->onlyTrashed();
        }

        $comments = $this->model
            ->field(['comment_id', 'user_id', 'commentable_id', 'commentable_type'])
            ->select($commentIds);

        if (!$comments) {
            return;
        }

        $this->doDestroy($comments);
    }
}
