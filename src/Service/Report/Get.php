<?php

declare(strict_types=1);

namespace MDClub\Service\Report;

use MDClub\Traits\Getable;

/**
 * 获取举报
 *
 * @property-read \MDClub\Model\Report $model;
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 获取举报列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }

    /**
     * 获取举报理由
     *
     * @param  string $reportableType
     * @param  int    $reportableId
     * @return array
     */
    public function getReasons(string $reportableType, int $reportableId): array
    {
        return $this->model
            ->where('reportable_type', $reportableType)
            ->where('reportable_id', $reportableId)
            ->order('create_time', 'DESC')
            ->paginate();
    }
}
