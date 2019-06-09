<?php

declare(strict_types=1);

namespace App\Service\Answer;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;

/**
 * 删除回答
 *
 * 涉及的配置项：
 * answer_can_delete                 是否可删除（总开关）
 * answer_can_delete_before          在发表后多少秒内可删除（0表示不作时间限制）
 * answer_can_delete_only_no_comment 仅在没有评论时可删除
 */
class Delete extends Abstracts
{
    /**
     * 软删除回答
     *
     * 1. 更新 delete_time
     *
     * @param int $answerId
     */
    public function delete(int $answerId): void
    {
        $userId = $this->roleService->userIdOrFail();
        $answer = $this->model
            ->field(['user_id', 'question_id', 'create_time'])
            ->where('delete_time', 0)
            ->where('answer_id', $answerId)
            ->get();

        if (!$answer) {
            return;
        }

        // 检查删除权限
        if (!$this->roleService->managerId()) {
            if ($answer['user_id'] !== $userId) {
                throw new ApiException(ErrorConstant::ANSWER_CANT_DELETE_NOT_AUTHOR);
            }

            $canDelete = $this->optionService->answer_can_delete;
            $canDeleteBefore = $this->optionService->answer_can_delete_before;
            $canDeleteOnlyNoComment = $this->optionService->answer_can_delete_only_no_comment;
            $requestTime = $this->requestService->time();

            if (!$canDelete) {
                throw new ApiException(ErrorConstant::ANSWER_CANT_DELETE);
            }

            if ($canDeleteBefore && $answer['create_time'] + (int) $canDeleteBefore < $requestTime) {
                throw new ApiException(ErrorConstant::ANSWER_CANT_DELETE_TIMEOUT);
            }

            if ($canDeleteOnlyNoComment && $answer['comment_count']) {
                throw new ApiException(ErrorConstant::ANSWER_CANT_DELETE_HAS_COMMENT);
            }
        }

        $this->model->delete($answerId);

        // 作者的 answer_count - 1
        $this->userModel
            ->where('user_id', $answer['user_id'])
            ->dec('answer_count')
            ->update();

        // 该提问的 answer_count - 1
        $this->questionModel
            ->where('question_id', $answer['question_id'])
            ->dec('answer_count')
            ->update();
    }

    /**
     * 批量软删除回答
     *
     * @param array $answerIds
     */
    public function deleteMultiple(array $answerIds): void
    {
        $userId = $this->roleService->userIdOrFail();

        if (!$answerIds) {
            return;
        }

        $answers = $this->model
            ->field(['question_id', 'user_id', 'answer_id'])
            ->where('delete_time', 0)
            ->where('answer_id', $answerIds)
            ->select();

        if (!$answers) {
            return;
        }

        $isManager = $this->roleService->managerId();

        if (!$isManager && collect($answers)->pluck('user_id')->diff([$userId])->count()) {
            $this->throwOnlyAuthorCanDeleteException();
        }

        $this->model->delete(array_column($answers, 'answer_id'));

        // 作者的 answer_count - 1、提问的 answer_count - 1
        $users = [];
        $questions = [];

        foreach ($answers as $answer) {
            $userId = $answer['user_id'];
            $questionId = $answer['question_id'];

            isset($users[$userId]) ? ++$users[$userId] : $users[$userId] = 1;
            isset($questions[$questionId]) ? ++$questions[$questionId] : $questions[$questionId] = 1;
        }

        foreach ($users as $userId => $count) {
            $this->userModel
                ->where('user_id', $userId)
                ->dec('answer_count', $count)
                ->update();
        }

        foreach ($questions as $questionId => $count) {
            $this->questionModel
                ->where('question_id', $questionId)
                ->dec('answer_count', $count)
                ->update();
        }
    }

    /**
     * 恢复回答
     *
     * @param int $answerId
     */
    public function restore(int $answerId): void
    {
        $this->roleService->managerIdOrFail();

        $answer = $this->model
            ->field(['user_id', 'question_id'])
            ->where('delete_time[>]', 0)
            ->where('answer_id', $answerId)
            ->get();

        if (!$answer) {
            $this->throwNotFoundException();
        }

        $this->model
            ->where('answer_id', $answerId)
            ->update('delete_time', 0);

        $this->userModel
            ->where('user_id', $answer['user_id'])
            ->inc('answer_count')
            ->update();

        $this->questionModel
            ->where('question_id', $answer['question_id'])
            ->inc('answer_count')
            ->update();
    }

    /**
     * 批量恢复回答
     *
     * @param array $answerIds
     */
    public function restoreMultiple(array $answerIds): void
    {
        $this->roleService->managerIdOrFail();

        if (!$answerIds) {
            return;
        }

        $answers = $this->model
            ->field(['question_id', 'user_id', 'answer_id'])
            ->where('delete_time[>]', 0)
            ->where('answer_id', $answerIds)
            ->select();

        if (!$answers) {
            return;
        }

        $this->model
            ->where('answer_id', array_column($answers, 'answer_id'))
            ->update('delete_time', 0);

        // 作者的 answer_count + 1、提问的 answer_count + 1
        $users = [];
        $questions = [];

        foreach ($answers as $answer) {
            $userId = $answer['user_id'];
            $questionId = $answer['question_id'];

            isset($users[$userId]) ? ++$users[$userId] : $users[$userId] = 1;
            isset($questions[$questionId]) ? ++$questions[$questionId] : $questions[$questionId] = 1;
        }

        foreach ($users as $userId => $count) {
            $this->userModel
                ->where('user_id', $userId)
                ->inc('answer_count', $count)
                ->update();
        }

        foreach ($questions as $questionId => $count) {
            $this->questionModel
                ->where('question_id', $questionId)
                ->inc('answer_count', $count)
                ->update();
        }
    }

    /**
     * 硬删除回答
     *
     * @param int $answerId
     */
    public function destroy(int $answerId): void
    {
        $this->roleService->managerIdOrFail();
        $answer = $this->getOrFail($answerId);

        $this->model->force()->delete($answerId);

        if (!$answer['delete_time']) {
            // 作者的 answer_count - 1
            $this->userModel
                ->where('user_id', $answer['user_id'])
                ->dec('answer_count')
                ->update();

            // 该提问的 answer_count - 1
            $this->questionModel
                ->where('question_id', $answer['question_id'])
                ->dec('answer_count')
                ->update();
        }

        // 删除该回答下的所有评论
        $this->commentModel
            ->where('commentable_type', 'answer')
            ->where('commentable_id', $answerId)
            ->force()
            ->delete();
    }

    /**
     * 批量硬删除回答
     *
     * @param array $answerIds
     */
    public function destroyMultiple(array $answerIds): void
    {
        $this->roleService->managerIdOrFail();

        if (!$answerIds) {
            return;
        }

        $answers = $this->model
            ->field(['question_id', 'user_id', 'answer_id', 'delete_time'])
            ->select($answerIds);

        if (!$answers) {
            return;
        }

        $answerIds = array_column($answers, 'answer_id');
        $this->model->force()->delete($answerIds);

        // 作者的 answer_count - 1、提问的 answer_count - 1
        $users = [];
        $questions = [];

        foreach ($answers as $answer) {
            $userId = $answer['user_id'];
            $questionId = $answer['question_id'];

            if (!$answer['delete_time']) {
                isset($users[$userId])
                    ? ++$users[$userId]
                    : $users[$userId] = 1;

                isset($questions[$questionId])
                    ? ++$questions[$questionId]
                    : $questions[$questionId] = 1;
            }
        }

        foreach ($users as $userId => $count) {
            $this->userModel
                ->where('user_id', $userId)
                ->dec('answer_count', $count)
                ->update();
        }

        foreach ($questions as $questionId => $count) {
            $this->questionModel
                ->where('question_id', $questionId)
                ->dec('answer_count', $count)
                ->update();
        }

        // 删除这些回答下的所有评论
        $this->commentModel
            ->where('commentable_type', 'answer')
            ->where('commentable_id', $answerIds)
            ->force()
            ->delete();
    }
}
