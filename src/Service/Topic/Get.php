<?php

declare(strict_types=1);

namespace MDClub\Service\Topic;

use MDClub\Traits\Getable;

/**
 * 获取话题
 *
 * @property-read \MDClub\Model\Topic $model
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 获取话题列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }

    /**
     * 获取已删除的话题列表
     *
     * @return array
     */
    public function getDeleted(): array
    {
        return $this->model->getDeleted();
    }
}
