<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;
use App\Helper\ArrayHelper;
use App\Helper\HtmlHelper;
use App\Helper\MarkdownHelper;
use App\Helper\ValidatorHelper;
use App\Traits\Commentable;
use App\Traits\Base;
use App\Traits\Votable;

/**
 * 对提问的回答
 *
 * @property-read \App\Model\Answer      currentModel
 *
 * Class Answer
 * @package App\Service
 */
class Answer extends ServiceAbstracts
{
    use Base, Commentable, Votable;

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
     * @param  array $condition
     * 三个参数中仅可指定一个
     * [
     *     'user_id'     => '',
     *     'question_id' => '',
     *     'is_deleted'  => true, // 该值为 true 时，获取已删除的记录；否则获取未删除的记录
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

        elseif (isset($condition['question_id'])) {
            $this->container->questionService->hasOrFail($condition['question_id']);
            $where = ['question_id' => $condition['question_id']];
        }

        elseif (isset($condition['is_deleted']) && $condition['is_deleted']) {
            $this->container->answerModel->onlyTrashed();

            $defaultOrder = ['delete_time' => 'DESC'];
            $allowOrderFields = ArrayHelper::push($this->getAllowOrderFields(), 'delete_time');
            $order = $this->getOrder($defaultOrder, $allowOrderFields);
        }

        $list = $this->container->answerModel
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
     * 发表回答
     *
     * @param  int    $questionId       提问ID
     * @param  string $contentMarkdown  Markdown格式正文
     * @param  string $contentRendered  HTML格式正文
     * @return int                      回答ID
     */
    public function create(int $questionId, string $contentMarkdown, string $contentRendered): int
    {
        $userId = $this->container->roleService->userIdOrFail();
        $this->container->questionService->hasOrFail($questionId);

        $data = $this->handleContent($contentMarkdown, $contentRendered);
        $data['question_id'] = $questionId;
        $data['user_id'] = $userId;

        // 添加回答
        $answerId = (int)$this->container->answerModel->insert($data);

        // 用户的 answer_count + 1
        $this->container->userModel
            ->where(['user_id' => $userId])
            ->update(['answer_count[+]' => 1]);

        // 更新提问的 answer_count 和 last_answer_time 字段
        $this->container->questionModel
            ->where(['question_id' => $questionId])
            ->update([
                'answer_count[+]' => 1,
                'last_answer_time' => $this->container->request->getServerParam('REQUEST_TIME'),
            ]);

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
        $userId = $this->container->roleService->userIdOrFail();
        $answerInfo = $this->container->answerModel->get($answerId);

        if (!$answerInfo) {
            throw new ApiException(ErrorConstant::ANSWER_NOT_FOUND);
        }

        if ($answerInfo['user_id'] != $userId && !$this->container->roleService->managerId()) {
            throw new ApiException(ErrorConstant::ANSWER_ONLY_AUTHOR_CAN_EDIT);
        }

        if (is_null($contentMarkdown) && is_null($contentRendered)) {
            return;
        }

        $content = $this->handleContent($contentMarkdown, $contentRendered);

        // 更新回答信息
        if ($content) {
            $this->container->answerModel
                ->where(['answer_id' => $answerId])
                ->update($content);
        }
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

    /**
     * 软删除回答
     *
     * @param int $answerId
     */
    public function delete(int $answerId): void
    {
        $userId = $this->container->roleService->userIdOrFail();
        $answerInfo = $this->container->answerModel
            ->field(['user_id', 'question_id'])
            ->get($answerId);

        if (!$answerInfo) {
            return;
        }

        if ($answerInfo['user_id'] != $userId && !$this->container->roleService->managerId()) {
            throw new ApiException(ErrorConstant::ANSWER_ONLY_AUTHOR_CAN_DELETE);
        }

        $this->container->answerModel->delete($answerId);

        // 该回答的作者的 answer_count - 1
        $this->container->userModel
            ->where(['user_id' => $answerInfo['user_id']])
            ->update(['answer_count[-]' => 1]);

        // 该提问的 answer_count - 1
        $this->container->questionModel
            ->where(['question_id' => $answerInfo['question_id']])
            ->update(['answer_count[-]' => 1]);
    }

    /**
     * 批量软删除回答
     *
     * @param array $answerIds
     */
    public function deleteMultiple(array $answerIds): void
    {
        if (!$answerIds) {
            return;
        }

        $answers = $this->container->answerModel
            ->field(['question_id', 'user_id', 'answer_id'])
            ->select($answerIds);

        if (!$answers) {
            return;
        }

        $answerIds = array_column($answers, 'answer_id');
        $this->container->answerModel->delete($answerIds);

        // 这些回答的作者、这些回答的提问的 answer_count - 1
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
            $this->container->userModel
                ->where(['user_id' => $userId])
                ->update(['answer_count[-]' => $count]);
        }

        foreach ($questions as $questionId => $count) {
            $this->container->questionModel
                ->where(['question_id' => $questionId])
                ->update(['answer_count[-]' => $count]);
        }
    }

    /**
     * 恢复回答
     *
     * @param int $answerId
     */
    public function restore(int $answerId): void
    {

    }

    /**
     * 批量恢复回答
     *
     * @param array $answerIds
     */
    public function restoreMultiple(array $answerIds): void
    {

    }

    /**
     * 硬删除回答
     *
     * @param int $answerId
     */
    public function destroy(int $answerId): void
    {

    }

    /**
     * 批量硬删除回答
     *
     * @param array $answerIds
     */
    public function destroyMultiple(array $answerIds): void
    {

    }

    /**
     * 对数据库中取出的回答信息进行处理
     * // todo 对回答进行处理
     *
     * @param  array $answers 回答信息，或多个回答组成的数组
     * @return array
     */
    public function handle(array $answers): array
    {
        if (!$answers) {
            return $answers;
        }

        if (!$isArray = is_array(current($answers))) {
            $answers = [$answers];
        }

        foreach ($answers as &$answer) {
        }

        if ($isArray) {
            return $answers;
        }

        return $answers[0];
    }

    /**
     * 获取在 relationship 中使用的 answer
     *
     * @param  array $answerIds
     * @return array
     */
    public function getInRelationship(array $answerIds): array
    {
        $answers = array_combine($answerIds, array_fill(0, count($answerIds), []));

        $answersTmp = $this->container->answerModel
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

        $votings = $this->container->voteService->getVotingInRelationship($answerIds, 'answer');
        $users = $this->container->userService->getInRelationship($userIds);
        $questions = $this->container->questionService->getInRelationship($questionIds);

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
