<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Constant\OptionConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Option;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\QuestionModel;
use MDClub\Facade\Service\QuestionService;
use MDClub\Validator\Traits\Markdown;

/**
 * 管理员可以编辑所有的提问，普通用户是否可编辑自己的提问由配置项设置
 *
 * 涉及的配置项：
 * question_can_edit                 是否可编辑（总开关）
 * question_can_edit_before          在发表后的多少秒内可编辑（0表示不作时间限制）
 * question_can_edit_only_no_comment 仅在没有评论时可编辑
 * question_can_edit_only_no_answer  仅在没有回答时可编辑
 *
 *
 * 管理员可以编辑所有的提问，普通用户是否可编辑自己的提问由配置项设置
 *
 * 涉及的配置项：
 * question_can_delete                 是否可删除（总开关）
 * question_can_delete_before          在发表后的多少秒内可删除（0表示不作时间限制）
 * question_can_delete_only_no_comment 仅在没有评论时可删除
 * question_can_delete_only_no_answer  仅在没有回答时可删除
 */
class Question extends Abstracts
{
    use Markdown;

    protected $attributes = [
        'title' => '提问标题',
        'content_markdown' => '提问内容',
        'content_rendered' => '提问内容',
        'topic_ids' => '所属话题'
    ];

    /**
     * 创建时验证
     *
     * @param  array $data [title, content_markdown, content_rendered, topic_id]
     * @return array
     */
    public function create(array $data): array
    {
        return $this->data($data)
            ->field('title')->exist()->stripTags()->trim()->notEmpty()->length(2, 80)->htmlentities()
            ->field('content')->markdownExist()->markdownSupport(100000)
            ->field('topic_ids')->exist()->arrayUnique()->arrayLength(1, 10)->topicIdsExist()
            ->validate();
    }

    /**
     * 更新时验证
     *
     * @param  int   $questionId
     * @param  array $data
     * @return array
     */
    public function update(int $questionId, array $data): array
    {
        $this->checkUpdatePermissions($questionId);

        return $this->data($data)
            ->field('title')->stripTags()->trim()->notEmpty()->length(2, 80)->htmlentities()
            ->field('content')->markdownSupport(100000)
            ->field('topic_ids')->arrayUnique()->arrayLength(1, 10)->topicIdsExist()
            ->validate();
    }

    /**
     * 删除前验证
     *
     * @param int $questionId
     *
     * @return array 提问信息
     */
    public function delete(int $questionId): array
    {
        return $this->checkDeletePermissions($questionId);
    }

    /**
     * 检查是否有权限进行更新
     *
     * 无权限时直接抛出异常，未抛出异常则表示有权限进行更新
     *
     * @param int $questionId
     */
    protected function checkUpdatePermissions(int $questionId): void
    {
        $question = QuestionService::getOrFail($questionId);

        if (Auth::isManager()) {
            return;
        }

        if ($question['user_id'] !== Auth::userId()) {
            throw new ApiException(ApiErrorConstant::QUESTION_CANT_EDIT_NOT_AUTHOR);
        }

        $canEdit = Option::get(OptionConstant::QUESTION_CAN_EDIT);
        $canEditBefore = (int) Option::get(OptionConstant::QUESTION_CAN_EDIT_BEFORE);
        $canEditOnlyNoComment = Option::get(OptionConstant::QUESTION_CAN_EDIT_ONLY_NO_COMMENT);
        $canEditOnlyNoAnswer = Option::get(OptionConstant::QUESTION_CAN_EDIT_ONLY_NO_ANSWER);

        if (!$canEdit) {
            throw new ApiException(ApiErrorConstant::QUESTION_CANT_EDIT);
        }

        if ($canEditBefore && (int) $question['create_time'] + $canEditBefore < Request::time()) {
            throw new ApiException(ApiErrorConstant::QUESTION_CANT_EDIT_TIMEOUT);
        }

        if ($canEditOnlyNoComment && $question['comment_count']) {
            throw new ApiException(ApiErrorConstant::QUESTION_CANT_EDIT_HAS_COMMENT);
        }

        if ($canEditOnlyNoAnswer && $question['answer_count']) {
            throw new ApiException(ApiErrorConstant::QUESTION_CANT_EDIT_HAS_ANSWER);
        }
    }

    /**
     * 检查是否有权限进行删除
     *
     * 无权限时直接抛出异常，未抛出异常则表示有权限进行删除
     *
     * 若资源不存在，也不会抛出异常
     *
     * @param int $questionId
     *
     * @return array 提问信息
     */
    protected function checkDeletePermissions(int $questionId): array
    {
        $question = QuestionModel::force()->get($questionId);

        if (!$question) {
            throw new ApiException(ApiErrorConstant::QUESTION_NOT_FOUND);
        }

        if (Auth::isManager()) {
            return $question;
        } elseif ($question['delete_time']) {
            throw new ApiException(ApiErrorConstant::QUESTION_NOT_FOUND);
        }

        if ($question['user_id'] !== Auth::userId()) {
            throw new ApiException(ApiErrorConstant::QUESTION_CANT_DELETE_NOT_AUTHOR);
        }

        $canDelete = Option::get(OptionConstant::QUESTION_CAN_DELETE);
        $canDeleteBefore = (int) Option::get(OptionConstant::QUESTION_CAN_DELETE_BEFORE);
        $canDeleteOnlyNoComment = Option::get(OptionConstant::QUESTION_CAN_DELETE_ONLY_NO_COMMENT);
        $canDeleteOnlyNoAnswer = Option::get(OptionConstant::QUESTION_CAN_DELETE_ONLY_NO_ANSWER);

        if (!$canDelete) {
            throw new ApiException(ApiErrorConstant::QUESTION_CANT_DELETE);
        }

        if ($canDeleteBefore && (int) $question['create_time'] + $canDeleteBefore < Request::time()) {
            throw new ApiException(ApiErrorConstant::QUESTION_CANT_DELETE_TIMEOUT);
        }

        if ($canDeleteOnlyNoComment && $question['comment_count']) {
            throw new ApiException(ApiErrorConstant::QUESTION_CANT_DELETE_HAS_COMMENT);
        }

        if ($canDeleteOnlyNoAnswer && $question['answer_count']) {
            throw new ApiException(ApiErrorConstant::QUESTION_CANT_DELETE_HAS_ANSWER);
        }

        return $question;
    }
}
