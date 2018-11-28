<?php

declare(strict_types=1);

namespace App\Controller;


use App\Abstracts\ControllerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 举报
 *
 * Class ReportController
 * @package App\Controller
 */
class ReportController extends ControllerAbstracts
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
     * 批量删除举报
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function batchDelete(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $target = $request->getQueryParam('target');

        if ($target) {
            $target = array_unique(array_filter(array_slice(explode(',', $target), 0, 100)));
        }

        if ($target) {
            $this->reportService->batchDelete($target);
        }

        return $this->success($response);
    }

    /**
     * 获取指定对象的举报详情
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $reportable_type
     * @param  int      $reportable_id
     * @return Response
     */
    public function getDetailList(Request $request, Response $response, string $reportable_type, int $reportable_id): Response
    {
        $this->roleService->managerIdOrFail();

        $list = $this->reportService->getDetailList($reportable_type, $reportable_id, true);

        return $this->success($response, $list);
    }

    /**
     * 创建举报
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $reportable_type
     * @param  int      $reportable_id
     * @return Response
     */
    public function create(Request $request, Response $response, string $reportable_type, int $reportable_id): Response
    {
        $reason = $request->getParsedBodyParam('reason');
        $reportId = $this->reportService->create($reportable_type, $reportable_id, $reason);
        $report = $this->reportService->get($reportId, true);

        return $this->success($response, $report);
    }

    /**
     * 删除举报
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $reportable_type
     * @param  int      $reportable_id
     * @return Response
     */
    public function delete(Request $request, Response $response, string $reportable_type, int $reportable_id): Response
    {
        $this->roleService->managerIdOrFail();

        $this->reportService->delete($reportable_type, $reportable_id);

        return $this->success($response);
    }
}
