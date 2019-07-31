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
     * 根据 topic_id 获取提问列表
     *
     * @param  int    $topicId
     * @return array
     */
    public function getByTopicId(int $topicId)
    {
        $this->topicGetService->hasOrFail($topicId);

        return $this->questionModel->getByTopicId($topicId);
    }

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
     * 获取未删除的提问列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }
}
