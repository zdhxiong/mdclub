<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 举报
 */
class Report extends ContainerAbstracts
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

        return $this->reportService
            ->fetchCollection()
            ->getList(true)
            ->render($response);
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
        $this->roleService->managerIdOrFail();

        $target = $this->requestService->getQueryParamToArray('target', 100);
        $this->reportService->deleteMultiple($target);

        return collect()->render($response);
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

        return $this->reportService
            ->fetchCollection()
            ->getDetailList($reportable_type, $reportable_id, true)
            ->render($response);
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

        return $this->reportService
            ->fetchCollection()
            ->get($reportId, true)
            ->render($response);
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
        $this->roleService->managerIdOrFail();

        $this->reportService->delete($reportable_type, $reportable_id);

        return collect()->render($response);
    }
}
