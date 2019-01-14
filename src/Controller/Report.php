<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use App\Helper\ArrayHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 举报
 *
 * Class Report
 * @package App\Controller
 */
class Report extends ControllerAbstracts
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
        $this->container->roleService->managerIdOrFail();

        $list = $this->container->reportService->getList(true);

        return $this->success($response, $list);
    }

    /**
     * 批量删除举报
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function deleteMultiple(Request $request, Response $response): Response
    {
        $this->container->roleService->managerIdOrFail();

        $target = ArrayHelper::getQueryParam($request, 'target', 100);
        $this->container->reportService->deleteMultiple($target);

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
        $this->container->roleService->managerIdOrFail();

        $list = $this->container->reportService->getDetailList($reportable_type, $reportable_id, true);

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
        $reportId = $this->container->reportService->create($reportable_type, $reportable_id, $reason);
        $report = $this->container->reportService->get($reportId, true);

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
    public function deleteOne(Request $request, Response $response, string $reportable_type, int $reportable_id): Response
    {
        $this->container->roleService->managerIdOrFail();

        $this->container->reportService->delete($reportable_type, $reportable_id);

        return $this->success($response);
    }
}
