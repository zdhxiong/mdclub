<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\Abstracts;
use MDClub\Helper\Request;
use MDClub\Middleware\NeedManager;

/**
 * 举报
 */
class Report extends Abstracts
{
    /**
     * 获取举报列表
     *
     * @uses NeedManager
     * @return array
     */
    public function getList(): array
    {
        return $this->reportGetService->getList();
    }

    /**
     * 批量删除举报
     *
     * @uses NeedManager
     * @return array
     */
    public function deleteMultiple(): array
    {
        $target = Request::getQueryParamToArray($this->request, 'target', 100);

        $this->reportDeleteService->deleteMultiple($target);

        return [];
    }

    /**
     * 获取指定对象的举报详情
     *
     * @uses NeedManager
     * @param  string   $reportable_type
     * @param  int      $reportable_id
     * @return array
     */
    public function getReasons(string $reportable_type, int $reportable_id): array
    {
        return $this->reportGetService->getReasons($reportable_type, $reportable_id);
    }

    /**
     * 创建举报
     *
     * @param  string   $reportable_type
     * @param  int      $reportable_id
     * @return array
     */
    public function create(string $reportable_type, int $reportable_id): array
    {
        $reason = $this->request->getParsedBody()['reason'] ?? '';
        $reportId = $this->reportCreateService->create($reportable_type, $reportable_id, $reason);

        return $this->reportGetService->get($reportId);
    }

    /**
     * 删除举报
     *
     * @uses NeedManager
     * @param  string   $reportable_type
     * @param  int      $reportable_id
     * @return array
     */
    public function delete(string $reportable_type, int $reportable_id): array
    {
        $this->reportDeleteService->delete($reportable_type, $reportable_id);

        return [];
    }
}
