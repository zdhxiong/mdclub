<?php

declare(strict_types=1);

namespace App\Service\Answer;

use App\Traits\Getable;
use Tightenco\Collect\Support\Collection;

/**
 * 获取回答
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return $this->roleService->managerId()
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
     * 对结果中的内容进行处理
     *
     * @param  array $answers
     * @return array
     */
    public function addFormatted(array $answers): array
    {
        return $answers;
    }

    /**
     * 为结果添加相关信息
     *
     * @param  array $answers
     * @return array
     */
    public function addRelationship(array $answers): array
    {
        $answerIds = array_unique(array_column($answers, 'answer_id'));
        $questionIds = array_unique(array_column($answers, 'question_id'));
        $userIds = array_unique(array_column($answers, 'user_id'));

        $votings = $this->voteService->getInRelationship($answerIds, 'answer');
        $users = $this->userGetService->getInRelationship($userIds);
        $questions = $this->questionGetService->getInRelationship($questionIds);

        foreach ($answers as &$answer) {
            $answer['relationship'] = [
                'user'     => $users[$answer['user_id']],
                'question' => $questions[$answer['question_id']],
                'voting'   => $votings[$answer['answer_id']],
            ];
        }

        return $answers;
    }

    /**
     * 根据 user_id 获取回答列表
     *
     * @param  int              $userId
     * @return array|Collection
     */
    public function getByUserId(int $userId)
    {
        $this->userGetService->hasOrFail($userId);

        $this->beforeGet();

        $result = $this->model
            ->where('user_id', $userId)
            ->order($this->getOrder(['create_time' => 'DESC']))
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 根据 question_id 获取回答列表
     *
     * @param  int              $questionId
     * @return array|Collection
     */
    public function getByQuestionId(int $questionId)
    {
        $this->questionGetService->hasOrFail($questionId);

        $this->beforeGet();

        $result = $this->model
            ->where('question_id', $questionId)
            ->order($this->getOrder(['create_time' => 'DESC']))
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 获取已删除的回答列表
     *
     * @return array|Collection
     */
    public function getDeleted()
    {
        $defaultOrder = ['delete_time' => 'DESC'];
        $allowOrderFields = collect($this->getAllowOrderFields())->push('delete_time')->unique()->all();
        $order = $this->getOrder($defaultOrder, $allowOrderFields);

        $this->beforeGet();

        $result = $this->model
            ->onlyTrashed()
            ->where($this->getWhere())
            ->order($order)
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 获取回答列表
     *
     * @return array|Collection
     */
    public function getList()
    {
        $this->beforeGet();

        $result = $this->model
            ->where($this->getWhere())
            ->order($this->getOrder(['create_time' => 'DESC']))
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
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

        return $this->model
            ->field(['answer_id', 'content_rendered', 'create_time', 'update_time'])
            ->fetchCollection()
            ->select($answerIds)
            ->keyBy('answer_id')
            ->map(static function ($item) {
                $item['content_summary'] = mb_substr(strip_tags($item['content_rendered']), 0, 80);

                return $item;
            })
            ->union($answers)
            ->all();
    }
}
