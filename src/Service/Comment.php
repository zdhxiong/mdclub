<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ArrayHelper;
use App\Helper\ValidatorHelper;
use App\Traits\Base;
use App\Traits\Votable;

/**
 * 评论
 *
 * @property-read \App\Model\Comment      currentModel
 *
 * Class Comment
 * @package App\Service
 */
class Comment extends ServiceAbstracts
{
    use Base, Votable;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return $this->container->roleService->managerId()
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
     * 获取评论列表
     *
     * @param  array $condition
     * 两个参数中仅可指定一个
     * [
     *     'user_id'    => '',
     *     'is_deleted' => true, // 该值为 true 时，获取已删除的记录；否则获取未删除的记录
     * ]
     * @param  bool  $withRelationship
     * @return array
     */
    public function getList(array $condition = [], bool $withRelationship = false): array
    {
        $where = $this->getWhere();
        $order = $this->getOrder(['create_time' => 'DESC']);

        if (isset($condition['user_id'])) {
            $this->container->userService->hasOrFail($condition['user_id']);
            $where = ['user_id' => $condition['user_id']];
        }

        elseif (isset($condition['is_deleted']) && $condition['is_deleted']) {
            $this->container->commentModel->onlyTrashed();

            $defaultOrder = ['delete_time' => 'DESC'];
            $allowOrderFields = ArrayHelper::push($this->getAllowOrderFields(), 'delete_time');
            $order = $this->getOrder($defaultOrder, $allowOrderFields);
        }

        $list = $this->container->commentModel
            ->where($where)
            ->order($order)
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
        $userId = $this->container->roleService->userIdOrFail();
        $commentInfo = $this->container->commentModel->get($commentId);

        if (!$commentInfo) {
            throw new ApiException(ErrorConstant::COMMENT_NOT_FOUND);
        }

        if ($commentInfo['user_id'] != $userId && !$this->container->roleService->managerId()) {
            throw new ApiException(ErrorConstant::COMMENT_ONLY_AUTHOR_CAN_EDIT);
        }

        $errors = [];

        if ($content) {
            $content = strip_tags($content);
        }

        // 验证内容
        if (is_null($content)) {
            return;
        }

        if (!$content) {
            $errors['content'] = '评论内容不能为空';
        } elseif (!ValidatorHelper::isMax($content, 1000)) {
            $errors['content'] = '评论内容不能超过 1000 个字符';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        $this->container->commentModel
            ->where(['comment_id' => $commentId])
            ->update(['content' => $content]);
    }

    /**
     * 删除评论
     *
     * @param int $commentId
     */
    public function delete(int $commentId): void
    {
        $userId = $this->container->roleService->userIdOrFail();
        $commentInfo = $this->container->commentModel
            ->field(['user_id', 'commentable_id', 'commentable_type'])
            ->get($commentId);

        if (!$commentInfo) {
            return;
        }

        if ($commentInfo['user_id'] != $userId && !$this->container->roleService->managerId()) {
            throw new ApiException(ErrorConstant::COMMENT_ONLY_AUTHOR_CAN_DELETE);
        }

        $this->container->commentModel->delete($commentId);

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
    public function deleteMultiple(array $commentIds): void
    {
        if (!$commentIds) {
            return;
        }

        $comments = $this->container->commentModel
            ->field(['comment_id', 'user_id', 'commentable_id', 'commentable_type'])
            ->select($commentIds);

        if (!$comments) {
            return;
        }

        $commentIds = array_column($comments, 'comment_id');
        $this->container->commentModel->delete($commentIds);

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

    /**
     * 对数据库中取出的评论数据进行处理
     * todo 处理评论
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

        $commentsTmp = $this->container->commentModel
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

        $votings = $this->container->voteService->getVotingInRelationship($commentIds, 'comment');
        $users = $this->container->userService->getInRelationship($userIds);

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
