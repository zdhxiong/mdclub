<?php

declare(strict_types=1);

namespace App\Service\Question;

use App\Traits\Getable;
use Tightenco\Collect\Support\Collection;

/**
 * 获取提问
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
        return ['question_id', 'user_id', 'topic_id']; // topic_id 需要另外写逻辑
    }

    /**
     * 对结果中的内容进行处理
     *
     * @param  array $questions
     * @return array
     */
    public function addFormatted(array $questions): array
    {
        return $questions;
    }

    /**
     * 为提问添加 relationship 字段
     * {
     *     user: {}
     *     topics: [ {}, {}, {} ]
     *     is_following: false
     *     voting: up, down, ''
     * }
     *
     * @param  array $questions
     * @param  array $knownRelationship ['is_following': bool]
     * @return array
     */
    public function addRelationship(array $questions, array $knownRelationship = []): array
    {
        $questionIds = array_unique(array_column($questions, 'question_id'));
        $userIds = array_unique(array_column($questions, 'user_id'));

        if (isset($knownRelationship['is_following'])) {
            $followingQuestionIds = $knownRelationship['is_following'] ? $questionIds : [];
        } else {
            $followingQuestionIds = $this->followService->getInRelationship($questionIds, 'question');
        }

        $votings = $this->voteService->getInRelationship($questionIds, 'question');
        $users = $this->userGetService->getInRelationship($userIds);
        $topics = $this->topicGetService->getInRelationship($questionIds, 'question');

        foreach ($questions as &$question) {
            $question['relationship'] = [
                'user'         => $users[$question['user_id']],
                'topics'       => $topics[$question['question_id']],
                'is_following' => in_array($question['question_id'], $followingQuestionIds, true),
                'voting'       => $votings[$question['question_id']],
            ];
        }

        return $questions;
    }

    /**
     * 根据 user_id 获取提问列表
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
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 根据 topic_id 获取提问列表
     *
     * @param  int              $topicId
     * @return array|Collection
     */
    public function getByTopicId(int $topicId)
    {
        $this->topicGetService->hasOrFail($topicId);

        $this->beforeGet();

        $result = $this->model
            ->join(['[><]topicable' => ['question_id' => 'topicable_id']])
            ->where('topicable.topicable_type', 'question')
            ->where('topicable.topic_id', $topicId)
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 获取 where
     *
     * @return array
     */
    protected function getWhereFromQuery(): array
    {
        $where = $this->getWhere();

        if (isset($where['topic_id'])) {
            $this->model->join(['[><]topicable' => ['question_id' => 'topicable_id']]);

            $where['topicable.topic_id'] = $where['topic_id'];
            $where['topicable.topicable_type'] = 'question';
            unset($where['topic_id']);
        }

        if (isset($where['user_id'])) {
            $where['question.user_id'] = $where['user_id'];
            unset($where['user_id']);
        }

        if (isset($where['question_id'])) {
            $where['question.question_id'] = $where['question_id'];
            unset($where['question_id']);
        }

        return $where;
    }

    /**
     * 获取已删除的提问列表
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
            ->where($this->getWhereFromQuery())
            ->order($order)
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 获取提问列表
     *
     * @return array|Collection
     */
    public function getList()
    {
        $this->beforeGet();

        $result = $this->model
            ->where($this->getWhereFromQuery())
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 获取在 relationship 中使用的 question
     *
     * @param  array $questionIds
     * @return array
     */
    public function getInRelationship(array $questionIds): array
    {
        $questions = array_combine($questionIds, array_fill(0, count($questionIds), []));

        return $this->model
            ->field(['question_id', 'title', 'create_time', 'update_time'])
            ->fetchCollection()
            ->select($questionIds)
            ->keyBy('question_id')
            ->union($questions)
            ->all();
    }
}
