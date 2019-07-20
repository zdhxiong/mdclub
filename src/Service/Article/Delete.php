<?php

declare(strict_types=1);

namespace MDClub\Service\Article;

/**
 * 删除文章
 */
class Delete extends Abstracts
{
    /**
     * 软删除文章
     *
     * @param  int  $postId
     */
    public function delete(int $postId): void
    {
        $userId = $this->roleService->userIdOrFail();

        $table = $this->getTableName();
        $pk = $this->getPrimaryKey();

        $post = $this->model
            ->field('user_id')
            ->where('delete_time', 0)
            ->where($pk, $postId)
            ->get();

        if (!$post) {
            return;
        }

        if ($post['user_id'] !== $userId && !$this->roleService->managerId()) {
            $this->throwOnlyAuthorCanDeleteException();
        }

        $this->model->delete($postId);

        // 该文章的作者的 post_count - 1
        $this->userModel
            ->where('user_id', $post['user_id'])
            ->dec("${table}_count")
            ->update();

        // 关注该文章的用户的 following_post_id - 1
        $followerIds = $this->followModel
            ->where('followable_type', $table)
            ->where('followable_id', $postId)
            ->pluck('user_id');

        $this->userModel
            ->where('user_id', $followerIds)
            ->dec("following_${table}_count")
            ->update();
    }

    /**
     * 批量软删除文章
     *
     * @param array $postIds
     */
    public function deleteMultiple(array $postIds): void
    {
        $userId = $this->roleService->userIdOrFail();

        $table = $this->getTableName();
        $pk = $this->getPrimaryKey();

        if (!$postIds) {
            return;
        }

        $posts = $this->model
            ->field([$pk, 'user_id'])
            ->where('delete_time', 0)
            ->where($pk, $postIds)
            ->select();

        if (!$posts) {
            return;
        }

        $isManager = $this->roleService->managerId();

        if (!$isManager && collect($posts)->pluck('user_id')->diff([$userId])->count()) {
            $this->throwOnlyAuthorCanDeleteException();
        }

        $postIds = array_column($posts, $pk);
        $this->model->delete($postIds);

        // 作者的 post_count - 1、关注者的 following_post_count - 1
        $users = [];
        $countField = "${table}_count";
        $followingCountField = "following_${countField}";

        foreach ($posts as $post) {
            $userId = $post['user_id'];

            isset($users[$userId][$countField])
                ? $users[$userId][$countField] += 1
                : $users[$userId][$countField] = 1;
        }

        $followerIds = $this->followModel
            ->where('followable_type', $table)
            ->where('followable_id', $postIds)
            ->pluck('user_id');

        foreach ($followerIds as $followerId) {
            isset($users[$followerId][$followingCountField])
                ? $users[$followerId][$followingCountField] += 1
                : $users[$followerId][$followingCountField] = 1;
        }

        $userModel = $this->userModel;

        foreach ($users as $userId => $user) {
            if (isset($user[$countField])) {
                $userModel->dec($countField, $user[$countField]);
            }

            if (isset($user[$followingCountField])) {
                $userModel->dec($followingCountField, $user[$followingCountField]);
            }

            $userModel->where('user_id', $userId)->update();
        }
    }

    /**
     * 恢复文章
     *
     * @param int $postId
     */
    public function restore(int $postId): void
    {
        $this->roleService->managerId();

        $table = $this->getTableName();
        $pk = $this->getPrimaryKey();

        $post = $this->model
            ->field('user_id')
            ->where('delete_time[>]', 0)
            ->where($pk, $postId)
            ->get();

        if (!$post) {
            $this->throwNotFoundException();
        }

        $this->model
            ->where($pk, $postId)
            ->update('delete_time', 0);

        // 作者的 post_count + 1
        $this->userModel
            ->where('user_id', $post['user_id'])
            ->inc("${table}_count")
            ->update();

        // 关注者的 following_post_id + 1
        $followerIds = $this->followModel
            ->where('followable_type', $table)
            ->where('followable_id', $postId)
            ->pluck('user_id');

        $this->userModel
            ->where('user_id', $followerIds)
            ->inc("following_${table}_count")
            ->update();
    }

    /**
     * 批量恢复文章
     *
     * @param array $postIds
     */
    public function restoreMultiple(array $postIds): void
    {
        $this->roleService->managerIdOrFail();

        $table = $this->getTableName();
        $pk = $this->getPrimaryKey();

        if (!$postIds) {
            return;
        }

        $posts = $this->model
            ->field([$pk, 'user_id'])
            ->where('delete_time[>]', 0)
            ->where($pk, $postIds)
            ->select();

        if (!$posts) {
            return;
        }

        $postIds = array_column($posts, $pk);

        $this->model
            ->where($pk, $postIds)
            ->update('delete_time', 0);

        // 作者的 post_count + 1、关注者的 following_post_id + 1
        $users = [];
        $countField = "${table}_count";
        $followingCountField = "following_${countField}";

        foreach ($posts as $post) {
            $userId = $post['user_id'];

            isset($users[$userId][$countField])
                ? $users[$userId][$countField] += 1
                : $users[$userId][$countField] = 1;
        }

        $followerIds = $this->followModel
            ->where('followable_type', $table)
            ->where('followable_id', $postIds)
            ->pluck('user_id');

        foreach ($followerIds as $followerId) {
            isset($users[$followerId][$followingCountField])
                ? $users[$followerId][$followingCountField] += 1
                : $users[$followerId][$followingCountField] = 1;
        }

        $userModel = $this->userModel;

        foreach ($users as $userId => $user) {
            if (isset($user[$countField])) {
                $userModel->inc($countField, $user[$countField]);
            }

            if (isset($user[$followingCountField])) {
                $userModel->inc($followingCountField, $user[$followingCountField]);
            }

            $userModel->where('user_id', $userId)->update();
        }
    }

    /**
     * 硬删除文章
     *
     * @param int $postId
     */
    public function destroy(int $postId): void
    {
        $this->roleService->managerIdOrFail();
        $post = $this->getOrFail($postId);

        $this->model->force()->delete($postId);

        // todo
        // post_count 和 following_post_count -1。 若已软删除，则无需修改
        // 删除所有关注关系
        // 删除提问和文章下所有评论
        // 删除提问下所有回答，和回答中的所有评论
        // 删除话题关系
        // 删除文章、提问、回答中的图片
        // 删除所有举报
        // 删除所有有关通知
        // 删除所有投票关系
    }

    /**
     * 批量硬删除文章
     *
     * @param array $postIds
     */
    public function destroyMultiple(array $postIds): void
    {

    }
}
