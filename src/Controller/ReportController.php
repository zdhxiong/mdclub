<?php

declare(strict_types=1);

namespace App\Controller;


use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 举报
 *
 * Class ReportController
 * @package App\Controller
 */
class ReportController extends Controller
{
    /**
     * 获取举报列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $list = $this->reportService->getList(true);

        return $this->success($response, $list);
    }

    /**
     * 创建举报
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $reportableId = $request->getParsedBodyParam('reportable_id');
        $reportableType = $request->getParsedBodyParam('reportable_type');
        $reason = $request->getParsedBodyParam('reason');

        $reportId = $this->reportService->create($userId, $reportableId, $reportableType, $reason);
        $report = $this->reportService->get($reportId, true);

        return $this->success($response, $report);
    }

    /**
     * 删除举报
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $report_id
     * @return Response
     */
    public function delete(Request $request, Response $response, int $report_id): Response
    {
        $this->roleService->managerIdOrFail();
        $this->reportService->delete($report_id);

        return $this->success($response);
    }

    /**
     * 批量删除举报
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function batchDelete(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $reportIds = $request->getQueryParam('report_id');

        if ($reportIds) {
            $reportIds = array_unique(array_filter(array_slice(explode(',', $reportIds), 0, 100)));
        }

        if ($reportIds) {
            $this->reportService->batchDelete($reportIds);
        }

        return $this->success($response);
    }
}
