<?php

declare(strict_types=1);

namespace MDClub\Service\Answer;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Helper\Request;

/**
 * 更新回答
 *
 * 管理员可以编辑所有的回答，普通用户是否可编辑自己的回答由配置项设置
 *
 * 涉及的配置项：
 * answer_can_edit                 是否可编辑（总开关）
 * answer_can_edit_before          在发表后的多少秒内可编辑（0表示不作时间限制）
 * answer_can_edit_only_no_comment 仅在没有评论时可编辑
 */
class Update extends Abstracts
{
    /**
     * 更新回答
     *
     * @param int    $answerId
     * @param string $contentMarkdown
     * @param string $contentRendered
     */
    public function update(int $answerId, string $contentMarkdown = null, string $contentRendered = null): void
    {
        $userId = $this->auth->userId();
        $answer = $this->answerGetService->getOrFail($answerId);

        // 检查编辑权限
        if ($this->auth->isNotManager()) {
            if ($answer['user_id'] !== $userId) {
                throw new ApiException(ApiError::ANSWER_CANT_EDIT_NOT_AUTHOR);
            }

            $canEdit = $this->option->answer_can_edit;
            $canEditBefore = $this->option->answer_can_edit_before;
            $canEditOnlyNoComment = $this->option->answer_can_edit_only_no_comment;
            $requestTime = Request::time($this->request);

            if (!$canEdit) {
                throw new ApiException(ApiError::ANSWER_CANT_EDIT);
            }

            if ($canEditBefore && $answer['create_time'] + (int) $canEditBefore < $requestTime) {
                throw new ApiException(ApiError::ANSWER_CANT_EDIT_TIMEOUT);
            }

            if ($canEditOnlyNoComment && $answer['comment_count']) {
                throw new ApiException(ApiError::ANSWER_CANT_EDIT_HAS_COMMENT);
            }
        }

        if ($contentMarkdown === null && $contentRendered === null) {
            return;
        }

        $content = $this->handleContent($contentMarkdown, $contentRendered);
        $this->model->where('answer_id', $answerId)->update($content);
    }
}
