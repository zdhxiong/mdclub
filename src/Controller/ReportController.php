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
     * 获取举报分组列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getGroupList(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $list = $this->reportService->getGroupList(true);

        return $this->success($response, $list);
    }

    /**
     * 批量删除举报分组
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function batchDeleteGroup(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $groups = $request->getQueryParam('group');

        if ($groups) {
            $groups = array_unique(array_filter(array_slice(explode(',', $groups), 0, 100)));
        }

        if ($groups) {
            $this->reportService->batchDeleteGroup($groups);
        }

        return $this->success($response);
    }

    /**
     * 删除指定举报分组
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $reportable_type
     * @param  int      $reportable_id
     * @return Response
     */
    public function deleteGroup(Request $request, Response $response, string $reportable_type, int $reportable_id): Response
    {
        $this->roleService->managerIdOrFail();

        $this->reportService->deleteGroup($reportable_type, $reportable_id);

        return $this->success($response);
    }

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
}
