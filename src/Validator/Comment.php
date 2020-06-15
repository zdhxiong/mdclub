<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Constant\OptionConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Option;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\CommentModel;
use MDClub\Facade\Service\CommentService;

/**
 * 管理员可编辑所有评论，普通用户是否可编辑自己的评论由配置项设置
 *
 * 涉及的配置项：
 * comment_can_edit         是否可编辑（总开关）
 * comment_can_edit_before  在发表后的多少秒内可编辑（0表示不作时间限制）
 *
 *
 * 管理员可删除所有评论，普通用户是否可删除自己的评论由配置项设置
 *
 * 涉及的配置项：
 * comment_can_delete         是否可编辑（总开关）
 * comment_can_delete_before  在发表后的多少秒内可删除（0表示不作时间限制）
 */
class Comment extends Abstracts
{
    protected $attributes = [
        'content' => '评论正文',
    ];

    /**
     * 创建时验证
     *
     * @param array  $data
     *
     * @return array
     */
    public function create(array $data): array
    {
        return $this->data($data)
            ->field('content')->exist()->trim()->notEmpty()->length(1, 1000)
            ->validate();
    }

    /**
     * 更新时验证
     *
     * @param int   $commentId
     * @param array $data [content]
     *
     * @return array
     */
    public function update(int $commentId, array $data): array
    {
        $this->checkUpdatePermissions($commentId);

        return $this->data($data)
            ->field('content')->trim()->notEmpty()->length(1, 1000)
            ->validate();
    }

    /**
     * 删除前验证
     *
     * @param int $commentId
     *
     * @return array 评论信息
     */
    public function delete(int $commentId): array
    {
        return $this->checkDeletePermissions($commentId);
    }

    /**
     * 检查是否有权限进行更新
     *
     * 无权限时直接抛出异常，未抛出异常则表示有权限进行更新
     *
     * @param int $commentId
     */
    protected function checkUpdatePermissions(int $commentId): void
    {
        $comment = CommentService::getOrFail($commentId);

        if (Auth::isManager()) {
            return;
        }

        if ($comment['user_id'] !== Auth::userId()) {
            throw new ApiException(ApiErrorConstant::COMMENT_CANT_EDIT_NOT_AUTHOR);
        }

        $canEdit = Option::get(OptionConstant::COMMENT_CAN_EDIT);
        $canEditBefore = (int) Option::get(OptionConstant::COMMENT_CAN_EDIT_BEFORE);

        if (!$canEdit) {
            throw new ApiException(ApiErrorConstant::COMMENT_CANT_EDIT);
        }

        if ($canEditBefore && (int) $comment['create_time'] + $canEditBefore < Request::time()) {
            throw new ApiException(ApiErrorConstant::COMMENT_CANT_EDIT_TIMEOUT);
        }
    }

    /**
     * 检查是否有权限进行删除
     *
     * 无权限时直接抛出异常，未抛出异常则表示有权限进行删除
     *
     * 资源不存在是不抛出异常
     *
     * @param int $commentId
     *
     * @return array 评论信息
     */
    protected function checkDeletePermissions(int $commentId): array
    {
        $comment = CommentModel::force()->get($commentId);

        if (!$comment) {
            throw new ApiException(ApiErrorConstant::COMMENT_NOT_FOUND);
        }

        if (Auth::isManager()) {
            return $comment;
        } elseif ($comment['delete_time']) {
            throw new ApiException(ApiErrorConstant::COMMENT_NOT_FOUND);
        }

        if ($comment['user_id'] !== Auth::userId()) {
            throw new ApiException(ApiErrorConstant::COMMENT_CANT_DELETE_NOT_AUTHOR);
        }

        $canDelete = Option::get(OptionConstant::COMMENT_CAN_DELETE);
        $canDeleteBefore = (int) Option::get(OptionConstant::COMMENT_CAN_DELETE_BEFORE);

        if (!$canDelete) {
            throw new ApiException(ApiErrorConstant::COMMENT_CANT_DELETE);
        }

        if ($canDeleteBefore && (int) $comment['create_time'] + $canDeleteBefore < Request::time()) {
            throw new ApiException(ApiErrorConstant::COMMENT_CANT_DELETE_TIMEOUT);
        }

        return $comment;
    }
}
