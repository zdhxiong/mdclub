<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Constant\OptionConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Option;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\AnswerModel;
use MDClub\Facade\Service\AnswerService;
use MDClub\Facade\Service\QuestionService;
use MDClub\Validator\Traits\Markdown;

/**
 * 管理员可以编辑所有的回答，普通用户是否可编辑自己的回答由配置项设置
 *
 * 涉及的配置项：
 * answer_can_edit                 是否可编辑（总开关）
 * answer_can_edit_before          在发表后的多少秒内可编辑（0表示不作时间限制）
 * answer_can_edit_only_no_comment 仅在没有评论时可编辑
 *
 *
 * 管理员可以删除所有的回答，普通用户是否可删除自己的回答由配置项设置
 *
 * 涉及的配置项：
 * answer_can_delete                 是否可删除（总开关）
 * answer_can_delete_before          在发表后的多少秒内可删除（0表示不作时间限制）
 * answer_can_delete_only_no_comment 仅在没有评论时可删除
 */
class Answer extends Abstracts
{
    use Markdown;

    protected $attributes = [
        'content_markdown' => '回答正文',
        'content_rendered' => '回答正文'
    ];

    /**
     * 创建时验证
     *
     * @param  array $data       [content_markdown, content_rendered]
     * @return array
     */
    public function create(array $data): array
    {
        return $this->data($data)
            ->field('content')->markdownExist()->markdownSupport(100000)
            ->validate();
    }

    /**
     * 更新时验证
     *
     * @param  int   $answerId
     * @param  array $data     [content_markdown, content_rendered]
     * @return array
     */
    public function update(int $answerId, array $data): array
    {
        $this->checkUpdatePermissions($answerId);

        return $this->data($data)
            ->field('content')->markdownSupport(100000)
            ->validate();
    }

    /**
     * 删除前验证
     *
     * @param int $answerId
     *
     * @return array 回答信息
     */
    public function delete(int $answerId): array
    {
        return $this->checkDeletePermissions($answerId);
    }

    /**
     * 检查是否有权限进行更新
     *
     * 无权限时直接抛出异常，未抛出异常则表示有权限进行更新
     *
     * @param int $answerId
     */
    protected function checkUpdatePermissions(int $answerId): void
    {
        $answer = AnswerService::getOrFail($answerId);

        if (Auth::isManager()) {
            return;
        }

        if ($answer['user_id'] !== Auth::userId()) {
            throw new ApiException(ApiErrorConstant::ANSWER_CANT_EDIT_NOT_AUTHOR);
        }

        $canEdit = Option::get(OptionConstant::ANSWER_CAN_EDIT);
        $canEditBefore = (int) Option::get(OptionConstant::ANSWER_CAN_EDIT_BEFORE);
        $canEditOnlyNoComment = Option::get(OptionConstant::ANSWER_CAN_EDIT_ONLY_NO_COMMENT);

        if (!$canEdit) {
            throw new ApiException(ApiErrorConstant::ANSWER_CANT_EDIT);
        }

        if ($canEditBefore && (int) $answer['create_time'] + $canEditBefore < Request::time()) {
            throw new ApiException(ApiErrorConstant::ANSWER_CANT_EDIT_TIMEOUT);
        }

        if ($canEditOnlyNoComment && $answer['comment_count']) {
            throw new ApiException(ApiErrorConstant::ANSWER_CANT_EDIT_HAS_COMMENT);
        }
    }

    /**
     * 检查是否有权限进行删除
     *
     * 无权限时抛出异常，未抛出异常则表示有权限进行删除
     *
     * 若资源不存在，也不抛出异常
     *
     * @param int $answerId
     *
     * @return array 回答信息
     */
    protected function checkDeletePermissions(int $answerId): array
    {
        $answer = AnswerModel::force()->get($answerId);

        if (!$answer) {
            throw new ApiException(ApiErrorConstant::ANSWER_NOT_FOUND);
        }

        if (Auth::isManager()) {
            return $answer;
        } elseif ($answer['delete_time']) {
            throw new ApiException(ApiErrorConstant::ANSWER_NOT_FOUND);
        }

        if ($answer['user_id'] !== Auth::userId()) {
            throw new ApiException(ApiErrorConstant::ANSWER_CANT_DELETE_NOT_AUTHOR);
        }

        $canDelete = Option::get(OptionConstant::ANSWER_CAN_DELETE);
        $canDeleteBefore = (int) Option::get(OptionConstant::ANSWER_CAN_DELETE_BEFORE);
        $canDeleteOnlyNoComment = Option::get(OptionConstant::ANSWER_CAN_DELETE_ONLY_NO_COMMENT);

        if (!$canDelete) {
            throw new ApiException(ApiErrorConstant::ANSWER_CANT_DELETE);
        }

        if ($canDeleteBefore && (int) $answer['create_time'] + $canDeleteBefore < Request::time()) {
            throw new ApiException(ApiErrorConstant::ANSWER_CANT_DELETE_TIMEOUT);
        }

        if ($canDeleteOnlyNoComment && $answer['comment_count']) {
            throw new ApiException(ApiErrorConstant::ANSWER_CANT_DELETE_HAS_COMMENT);
        }

        return $answer;
    }
}
