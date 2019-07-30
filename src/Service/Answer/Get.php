<?php

declare(strict_types=1);

namespace MDClub\Service\Answer;

use MDClub\Traits\Getable;

/**
 * 获取回答
 *
 * @property-read \MDClub\Model\Answer $model
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 根据 user_id 获取回答列表
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
     * 根据 question_id 获取回答列表
     *
     * @param  int   $questionId
     * @return array
     */
    public function getByQuestionId(int $questionId): array
    {
        $this->questionGetService->hasOrFail($questionId);

        return $this->model->getByQuestionId($questionId);
    }

    /**
     * 获取已删除的回答列表
     *
     * @return array
     */
    public function getDeleted(): array
    {
        return $this->model->getDeleted();
    }

    /**
     * 获取未删除的回答列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }
}
