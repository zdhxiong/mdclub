<?php

declare(strict_types=1);

namespace MDClub\Service\Question;

use MDClub\Traits\Getable;

/**
 * 获取提问
 *
 * @property-read \MDClub\Model\Question $model
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 根据 topic_id 获取提问列表
     *
     * @param  int              $topicId
     * @return array
     */
    /*public function getByTopicId(int $topicId)
    {
        $this->topicGetService->hasOrFail($topicId);

        return $this->model
            ->join(['[><]topicable' => ['question_id' => 'topicable_id']])
            ->where('topicable.topicable_type', 'question')
            ->where('topicable.topic_id', $topicId)
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();
    }*/

    /**
     * 获取 where
     *
     * @return array
     */
    /*protected function getWhereFromQuery(): array
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
    }*/

    /**
     * 获取已删除的提问列表
     *
     * @return array
     */
    /*public function getDeleted()
    {
        $defaultOrder = ['delete_time' => 'DESC'];
        $allowOrderFields = collect($this->getAllowOrderFields())->push('delete_time')->unique()->all();
        $order = $this->getOrder($defaultOrder, $allowOrderFields);

        return $this->model
            ->onlyTrashed()
            ->where($this->getWhereFromQuery())
            ->order($order)
            ->paginate();
    }*/

    /**
     * 根据 user_id 获取提问列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        $this->userGetService->hasOrFail($userId);

        return $this->model->getByUserId($userId);
    }

    /**
     * 获取未删除的提问列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }
}
