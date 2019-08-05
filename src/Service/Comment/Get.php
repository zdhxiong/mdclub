<?php

declare(strict_types=1);

namespace MDClub\Service\Comment;

use MDClub\Traits\Getable;

/**
 * 获取评论
 *
 * @property-read \MDClub\Model\Comment $model
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 根据 user_id 获取评论列表
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
     * 获取未删除的评论列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }

    /**
     * 获取已删除的评论列表
     *
     * @return array
     */
    public function getDeleted()
    {
        return $this->model->getDeleted();
    }
}
