<?php

declare(strict_types=1);

namespace App\Service\Answer;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\HtmlHelper;
use App\Helper\MarkdownHelper;
use App\Helper\ValidatorHelper;

/**
 * 添加、更新回答
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
     * 发表回答
     *
     * @param  int    $questionId       提问ID
     * @param  string $contentMarkdown  Markdown格式正文
     * @param  string $contentRendered  HTML格式正文
     * @return int                      回答ID
     */
    public function create(int $questionId, string $contentMarkdown, string $contentRendered): int
    {
        $userId = $this->roleService->userIdOrFail();
        $this->questionGetService->hasOrFail($questionId);

        $data = $this->handleContent($contentMarkdown, $contentRendered);
        $data['question_id'] = $questionId;
        $data['user_id'] = $userId;

        // 添加回答
        $answerId = (int) $this->model->insert($data);

        // 作者的 answer_count + 1
        $this->userModel
            ->where('user_id', $userId)
            ->inc('answer_count')
            ->update();

        // 更新提问的 answer_count 和 last_answer_time 字段
        $this->questionModel
            ->where('question_id', $questionId)
            ->inc('answer_count')
            ->set('last_answer_time', $this->requestService->time())
            ->update();

        return $answerId;
    }

    /**
     * 更新回答
     *
     * @param int    $answerId
     * @param string $contentMarkdown
     * @param string $contentRendered
     */
    public function update(int $answerId, string $contentMarkdown = null, string $contentRendered = null): void
    {
        $userId = $this->roleService->userIdOrFail();
        $answer = $this->answerGetService->getOrFail($answerId);

        // 检查编辑权限
        if (!$this->roleService->managerId()) {
            if ($answer['user_id'] !== $userId) {
                throw new ApiException(ErrorConstant::ANSWER_CANT_EDIT_NOT_AUTHOR);
            }

            $canEdit = $this->optionService->answer_can_edit;
            $canEditBefore = $this->optionService->answer_can_edit_before;
            $canEditOnlyNoComment = $this->optionService->answer_can_edit_only_no_comment;
            $requestTime = $this->requestService->time();

            if (!$canEdit) {
                throw new ApiException(ErrorConstant::ANSWER_CANT_EDIT);
            }

            if ($canEditBefore && $answer['create_time'] + (int) $canEditBefore < $requestTime) {
                throw new ApiException(ErrorConstant::ANSWER_CANT_EDIT_TIMEOUT);
            }

            if ($canEditOnlyNoComment && $answer['comment_count']) {
                throw new ApiException(ErrorConstant::ANSWER_CANT_EDIT_HAS_COMMENT);
            }
        }

        if ($contentMarkdown === null && $contentRendered === null) {
            return;
        }

        $content = $this->handleContent($contentMarkdown, $contentRendered);
        $this->model->where('answer_id', $answerId)->update($content);
    }

    /**
     * 发表回答前对参数进行验证
     *
     * @param  string $contentMarkdown
     * @param  string $contentRendered
     * @return array
     */
    private function handleContent(string $contentMarkdown, string $contentRendered): array
    {
        $contentMarkdown = HtmlHelper::removeXss($contentMarkdown);
        $contentRendered = HtmlHelper::removeXss($contentRendered);

        // content_markdown 和 content_rendered 至少需传入一个；都传入时，以 content_markdown 为准
        if (!$contentMarkdown && !$contentRendered) {
            $error = '正文不能为空';
        } elseif (!$contentMarkdown) {
            $contentMarkdown = HtmlHelper::toMarkdown($contentRendered);
        } else {
            $contentRendered = MarkdownHelper::toHtml($contentMarkdown);
        }

        // 验证正文长度
        $isTooLong = ValidatorHelper::isMin(strip_tags($contentRendered), 100000);
        if (empty($error) && $isTooLong) {
            $error = '正文不能超过 100000 个字';
        }

        if (!empty($error)) {
            throw new ValidationException([
                'content_markdown' => $error,
                'content_rendered' => $error,
            ]);
        }

        return [
            'content_markdown' => $contentMarkdown,
            'content_rendered' => $contentRendered,
        ];
    }
}
