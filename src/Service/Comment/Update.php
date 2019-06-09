<?php

declare(strict_types=1);

namespace App\Service\Comment;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;

/**
 * 更新评论
 *
 * 管理员可编辑所有评论，普通用户是否可编辑自己的评论由配置项设置
 *
 * 涉及的配置项：
 * comment_can_edit         是否可编辑（总开关）
 * comment_can_edit_before  在发表后的多少秒内可编辑（0表示不作时间限制）
 */
class Update extends Abstracts
{
    /**
     * 更新评论
     *
     * @param  int    $commentId 评论ID
     * @param  string $content   评论内容
     */
    public function update(int $commentId, string $content = null): void
    {
        $userId = $this->roleService->userIdOrFail();
        $comment = $this->commentGetService->getOrFail($commentId);

        // 检查编辑权限
        if (!$this->roleService->managerId()) {
            if ($comment['user_id'] !== $userId) {
                throw new ApiException(ErrorConstant::COMMENT_CANT_EDIT_NOT_AUTHOR);
            }

            $canEdit = $this->optionService->comment_can_edit;
            $canEditBefore = $this->optionService->comment_can_edit_before;
            $requestTime = $this->requestService->time();

            if (!$canEdit) {
                throw new ApiException(ErrorConstant::COMMENT_CANT_EDIT);
            }

            if ($canEditBefore && $comment['create_time'] + (int) $canEditBefore < $requestTime) {
                throw new ApiException(ErrorConstant::COMMENT_CANT_EDIT_TIMEOUT);
            }
        }

        if ($content === null) {
            return;
        }

        $content = $this->validContent($content);

        $this->model
            ->where('comment_id', $commentId)
            ->update('content', $content);
    }

    /**
     * 验证评论内容
     *
     * @param  string $content
     * @return string
     */
    public function validContent(string $content): string
    {
        $content = strip_tags($content);
        $errors = [];

        if (!$content) {
            $errors['content'] = '评论内容不能为空';
        } elseif (!ValidatorHelper::isMax($content, 1000)) {
            $errors['content'] = '评论内容不能超过 1000 个字符';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return $content;
    }
}
