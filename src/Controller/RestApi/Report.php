<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Facade\Library\Request;
use MDClub\Facade\Service\ReportService;

/**
 * 举报 API
 */
class Report extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getService(): string
    {
        return \MDClub\Service\Report::class;
    }

    /**
     * 获取举报列表
     *
     * @return array
     */
    public function getList(): array
    {
        return ReportService::getList();
    }

    /**
     * 批量删除举报
     *
     * @param array $reportTargets
     *
     * @return null
     */
    public function deleteMultiple(array $reportTargets)
    {
        ReportService::deleteMultiple($reportTargets);

        return null;
    }

    /**
     * 获取指定对象的举报详情
     *
     * @param  string   $reportableType
     * @param  int      $reportableId
     * @return array
     */
    public function getReasons(string $reportableType, int $reportableId): array
    {
        return ReportService::getReasons($reportableType, $reportableId);
    }

    /**
     * 创建举报
     *
     * @param  string   $reportableType
     * @param  int      $reportableId
     * @return array
     */
    public function create(string $reportableType, int $reportableId): array
    {
        $requestBody = Request::getParsedBody();
        $reportId = ReportService::create($reportableType, $reportableId, $requestBody);

        return ReportService::get($reportId);
    }

    /**
     * 删除举报
     *
     * @param  string   $reportableType
     * @param  int      $reportableId
     * @return null
     */
    public function delete(string $reportableType, int $reportableId)
    {
        ReportService::delete($reportableType, $reportableId);

        return null;
    }
}
