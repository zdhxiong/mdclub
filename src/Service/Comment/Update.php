<?php

declare(strict_types=1);

namespace MDClub\Service\Comment;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Exception\ValidationException;
use MDClub\Helper\Request;
use MDClub\Helper\Validator;

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
     * @var int 当前用户ID
     */
    protected $userId;

    /**
     * @var array 评论信息数组
     */
    protected $comment;

    /**
     * 验证评论内容
     *
     * @param  string $content
     * @return string
     */
    protected function validContent(string $content): string
    {
        $content = strip_tags($content);
        $errors = [];

        if (!$content) {
            $errors['content'] = '评论内容不能为空';
        } elseif (!Validator::isMax($content, 1000)) {
            $errors['content'] = '评论内容不能超过 1000 个字符';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return $content;
    }

    /**
     * 检查编辑权限
     */
    protected function checkPermission(): void
    {
        if ($this->comment['user_id'] !== $this->userId) {
            throw new ApiException(ApiError::COMMENT_CANT_EDIT_NOT_AUTHOR);
        }

        $canEdit = $this->option->comment_can_edit;
        $canEditBefore = (int) $this->option->comment_can_edit_before;
        $requestTime = Request::time($this->request);

        if (!$canEdit) {
            throw new ApiException(ApiError::COMMENT_CANT_EDIT);
        }

        if ($canEditBefore && $this->comment['create_time'] + $canEditBefore < $requestTime) {
            throw new ApiException(ApiError::COMMENT_CANT_EDIT_TIMEOUT);
        }
    }

    /**
     * 更新评论
     *
     * @param  int    $commentId 评论ID
     * @param  string $content   评论内容
     */
    public function update(int $commentId, string $content = null): void
    {
        $this->userId = $this->auth->userId();
        $this->comment = $this->commentGetService->getOrFail($commentId);

        if ($this->auth->isNotManager()) {
            $this->checkPermission();
        }

        if ($content === null) {
            return;
        }

        $content = $this->validContent($content);

        $this->model
            ->where('comment_id', $commentId)
            ->update('content', $content);
    }
}
