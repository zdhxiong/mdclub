<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\HtmlHelper;
use App\Helper\MarkdownHelper;
use App\Helper\ValidatorHelper;
use App\Traits\CommentableTraits;
use App\Traits\BaseTraits;
use App\Traits\VotableTraits;

/**
 * 对问题的回答
 *
 * @property-read \App\Model\AnswerModel      currentModel
 *
 * Class AnswerService
 * @package App\Service
 */
class AnswerService extends ServiceAbstracts
{
    use BaseTraits, CommentableTraits, VotableTraits;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return ['delete_time'];
    }

    /**
     * 获取允许排序的字段
     *
     * @return array
     */
    public function getAllowOrderFields(): array
    {
        return ['vote_count', 'create_time', 'update_time'];
    }

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return ['answer_id', 'question_id', 'user_id'];
    }

    /**
     * 获取回答列表
     *
     * @param  bool  $withRelationship
     * @return array
     */
    public function getList(bool $withRelationship = false): array
    {
        $list = $this->answerModel
            ->where($this->getWhere())
            ->order($this->getOrder(['create_time' => 'DESC']))
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        foreach ($list['data'] as &$item) {
            $item = $this->handle($item);
        }

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 根据用户ID获取回答列表
     *
     * @param  int   $userId
     * @param  bool  $withRelationship
     * @return array
     */
    public function getListByUserId(int $userId, bool $withRelationship = false): array
    {
        $this->userService->hasOrFail($userId);

        $list = $this->answerModel
            ->where(['user_id' => $userId])
            ->order($this->getOrder(['create_time' => 'DESC']))
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        foreach ($list['data'] as &$item) {
            $item = $this->handle($item);
        }

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 根据问题ID获取回答列表
     *
     * @param  int   $questionId
     * @param  bool  $withRelationship
     * @return array
     */
    public function getListByQuestionId(int $questionId, bool $withRelationship = false): array
    {
        $this->questionService->hasOrFail($questionId);

        $list = $this->answerModel
            ->where(['question_id' => $questionId])
            ->order($this->getOrder(['create_time' => 'DESC']))
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        foreach ($list['data'] as &$item) {
            $item = $this->handle($item);
        }

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 发表回答
     *
     * @param  int    $userId
     * @param  int    $questionId
     * @param  string $contentMarkdown
     * @param  string $contentRendered
     * @return int
     */
    public function create(
        int    $userId,
        int    $questionId,
        string $contentMarkdown,
        string $contentRendered
    ): int {
        [$contentMarkdown, $contentRendered] = $this->createValidator($questionId, $contentMarkdown, $contentRendered);

        // 添加回答
        $answerId = (int)$this->answerModel->insert([
            'question_id'      => $questionId,
            'user_id'          => $userId,
            'content_markdown' => $contentMarkdown,
            'content_rendered' => $contentRendered,
        ]);

        // 用户的 answer_count + 1
        $this->userModel
            ->where(['user_id' => $userId])
            ->update(['answer_count[+]' => 1]);

        // 更新问题的 answer_count 和 last_answer_time 字段
        $this->questionModel
            ->where(['question_id' => $questionId])
            ->update([
                'answer_count[+]' => 1,
                'last_answer_time' => $this->request->getServerParam('REQUEST_TIME'),
            ]);

        return $answerId;
    }

    /**
     * 发表回答前对参数进行验证
     *
     * @param  int    $questionId
     * @param  string $contentMarkdown
     * @param  string $contentRendered
     * @return array
     */
    private function createValidator(
        int    $questionId,
        string $contentMarkdown,
        string $contentRendered
    ): array {
        $this->questionService->hasOrFail($questionId);

        $errors = [];

        $contentMarkdown = HtmlHelper::removeXss($contentMarkdown);
        $contentRendered = HtmlHelper::removeXss($contentRendered);

        // content_markdown 和 content_rendered 至少需传入一个；都传入时，以 content_markdown 为准
        if (!$contentMarkdown && !$contentRendered) {
            $errors['content_markdown'] = $errors['content_rendered'] = '正文不能为空';
        } elseif (!$contentMarkdown) {
            $contentMarkdown = HtmlHelper::toMarkdown($contentRendered);
        } else {
            $contentRendered = MarkdownHelper::toHtml($contentMarkdown);
        }

        // 验证正文长度
        if (
               !isset($errors['content_markdown'])
            && !isset($errors['content_rendered'])
            && !ValidatorHelper::isMax(strip_tags($contentRendered), 100000)
        ) {
            $errors['content_markdown'] = $errors['content_rendered'] = '正文不能超过 100000 个字';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return [$contentMarkdown, $contentRendered];
    }

    /**
     * 更新回答
     *
     * @param int    $answerId
     * @param string $contentMarkdown
     * @param string $contentRendered
     */
    public function update(
        int    $answerId,
        string $contentMarkdown = null,
        string $contentRendered = null
    ): void {
        $data = $this->updateValidator($answerId, $contentMarkdown, $contentRendered);

        // 更新回答信息
        if ($data) {
            $this->answerModel
                ->where(['answer_id' => $answerId])
                ->update($data);
        }
    }

    /**
     * 更新回答前的字段验证
     *
     * @param int    $answerId
     * @param string $contentMarkdown
     * @param string $contentRendered
     * @return array
     */
    private function updateValidator(
        int    $answerId,
        string $contentMarkdown = null,
        string $contentRendered = null
    ): array {
        $data = [];

        $userId = $this->roleService->userIdOrFail();
        $answerInfo = $this->answerModel->get($answerId);

        if (!$answerInfo) {
            throw new ApiException(ErrorConstant::ANSWER_NOT_FOUND);
        }

        if ($answerInfo['user_id'] != $userId && !$this->roleService->managerId()) {
            throw new ApiException(ErrorConstant::ANSWER_ONLY_AUTHOR_CAN_EDIT);
        }

        $errors = [];

        if (!is_null($contentMarkdown) || !is_null($contentRendered)) {
            if (!is_null($contentMarkdown)) {
                $contentMarkdown = HtmlHelper::removeXss($contentMarkdown);
            }

            if (!is_null($contentRendered)) {
                $contentRendered = HtmlHelper::removeXss($contentRendered);
            }

            if (!$contentMarkdown && !$contentRendered) {
                $errors['content_markdown'] = $errors['content_rendered'] = '正文不能为空';
            } elseif (!$contentMarkdown) {
                $contentMarkdown = HtmlHelper::toMarkdown($contentRendered);
            } else {
                $contentRendered = MarkdownHelper::toHtml($contentMarkdown);
            }

            // 验证长度
            if (
                   !isset($errors['content_markdown'])
                && !isset($errors['content_rendered'])
                && !ValidatorHelper::isMax(strip_tags($contentRendered), 100000)
            ) {
                $errors['content_markdown'] = $errors['content_rendered'] = '正文不能超过 100000 个字';
            }

            if (!isset($errors['content_markdown']) && !isset($errors['content_rendered'])) {
                $data['content_markdown'] = $contentMarkdown;
                $data['content_rendered'] = $contentRendered;
            }
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return $data;
    }

    /**
     * 删除回答
     *
     * @param int $answerId
     */
    public function delete(int $answerId): void
    {
        $userId = $this->roleService->userIdOrFail();
        $answerInfo = $this->answerModel->field(['user_id', 'question_id'])->get($answerId);

        if (!$answerInfo) {
            return;
        }

        if ($answerInfo['user_id'] != $userId && !$this->roleService->managerId()) {
            throw new ApiException(ErrorConstant::ANSWER_ONLY_AUTHOR_CAN_DELETE);
        }

        $this->answerModel->delete($answerId);

        // 该回答的作者的 answer_count - 1
        $this->userModel
            ->where(['user_id' => $answerInfo['user_id']])
            ->update(['answer_count[-]' => 1]);

        // 该问题的 answer_count - 1
        $this->questionModel
            ->where(['question_id' => $answerInfo['question_id']])
            ->update(['answer_count[-]' => 1]);
    }

    /**
     * 批量删除回答
     *
     * @param array $answerIds
     */
    public function batchDelete(array $answerIds): void
    {
        $answers = $this->answerModel
            ->field(['question_id', 'user_id'])
            ->select($answerIds);

        if (!$answers) {
            return;
        }

        $answerIds = array_column($answers, 'answer_id');
        $this->answerModel->delete($answerIds);

        // 这些回答的作者、这些回答的问题的 answer_count - 1
        $users = [];
        $questions = [];

        foreach ($answers as $answer) {
            isset($users[$answer['user_id']])
                ? $users[$answer['user_id']] += 1
                : $users[$answer['user_id']] = 1;

            isset($questions[$answer['question_id']])
                ? $questions[$answer['question_id']] += 1
                : $questions[$answer['question_id']] = 1;
        }

        foreach ($users as $userId => $count) {
            $this->userModel
                ->where(['user_id' => $userId])
                ->update(['answer_count[-]' => $count]);
        }

        foreach ($questions as $questionId => $count) {
            $this->questionModel
                ->where(['question_id' => $questionId])
                ->update(['answer_count[-]' => $count]);
        }
    }

    /**
     * 对数据库中取出的回答信息进行处理
     *
     * @param  array $answerInfo
     * @return array
     */
    public function handle(array $answerInfo): array
    {
        return $answerInfo;
    }

    /**
     * 获取在 relationship 中使用的 answer
     *
     * @param  array $answerIds
     * @return array
     */
    public function getAnswersInRelationship(array $answerIds): array
    {
        $answers = array_combine($answerIds, array_fill(0, count($answerIds), []));

        $answersTmp = $this->answerModel
            ->field(['answer_id', 'content_rendered', 'create_time', 'update_time'])
            ->select($answerIds);

        foreach ($answersTmp as $item) {
            $answers[$item['answer_id']] = [
                'answer_id'       => $item['answer_id'],
                'content_summary' => mb_substr(strip_tags($item['content_rendered']), 0, 80),
                'create_time'     => $item['create_time'],
                'update_time'     => $item['update_time'],
            ];
        }

        return $answers;
    }

    /**
     * 为回答添加 relationship 字段
     * {
     *     user: {}
     *     question: {},
     *     voting: up、down、''
     * }
     *
     * @param  array $answers
     * @return array
     */
    public function addRelationship(array $answers): array
    {
        if (!$answers) {
            return $answers;
        }

        if (!$isArray = is_array(current($answers))) {
            $answers = [$answers];
        }

        $answerIds = array_unique(array_column($answers, 'answer_id'));
        $questionIds = array_unique(array_column($answers, 'question_id'));
        $userIds = array_unique(array_column($answers, 'user_id'));

        $votings = $this->voteService->getVotingInRelationship($answerIds, 'answer');
        $users = $this->userService->getUsersInRelationship($userIds);
        $questions = $this->questionService->getQuestionsInRelationship($questionIds);

        foreach ($answers as &$answer) {
            $answer['relationship'] = [
                'user'     => $users[$answer['user_id']],
                'question' => $questions[$answer['question_id']],
                'voting'   => $votings[$answer['answer_id']],
            ];
        }

        if ($isArray) {
            return $answers;
        }

        return $answers[0];
    }
}
