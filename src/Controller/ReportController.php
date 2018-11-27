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

        return $response;
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

        return $response;
    }

    /**
     * 删除指定举报分组
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  string   $group
     * @return Response
     */
    public function deleteGroup(Request $request, Response $response, string $group): Response
    {
        echo $group;
        exit;
        $this->roleService->managerIdOrFail();

        return $response;
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
