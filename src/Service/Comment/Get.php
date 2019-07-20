<?php

declare(strict_types=1);

namespace MDClub\Service\Comment;

use MDClub\Library\Collection;
use MDClub\Traits\Getable;

/**
 * 获取评论
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 执行查询列表操作
     *
     * @return array
     */
    protected function doGetList()
    {
        return $this->model->paginate();
    }


    /**
     * 获取已删除的评论列表
     *
     * @return array|Collection
     */
    public function getDeleted()
    {
        $defaultOrder = ['delete_time' => 'DESC'];
        $allowOrderFields = collect($this->getAllowOrderFields())->push('delete_time')->unique()->all();
        $order = $this->getOrder($defaultOrder, $allowOrderFields);

        $this->model
            ->onlyTrashed()
            ->where($this->getWhere())
            ->order($order);

        return $this->doGetList();
    }
}
