<?php

declare(strict_types=1);

namespace MDClub\Service\Question;

use MDClub\Library\Collection;
use MDClub\Traits\Getable;

/**
 * 获取提问
 */
class Get extends Abstracts
{
    use Getable;

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
     * 根据 topic_id 获取提问列表
     *
     * @param  int              $topicId
     * @return array|Collection
     */
    public function getByTopicId(int $topicId)
    {
        $this->topicGetService->hasOrFail($topicId);

        return $this->model
            ->join(['[><]topicable' => ['question_id' => 'topicable_id']])
            ->where('topicable.topicable_type', 'question')
            ->where('topicable.topic_id', $topicId)
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();
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

        return $this->model
            ->onlyTrashed()
            ->where($this->getWhereFromQuery())
            ->order($order)
            ->paginate();
    }

    /**
     * 获取提问列表
     *
     * @return array
     */
    public function getList()
    {
        return $this->model
            ->where($this->getWhereFromQuery())
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();
    }
}
