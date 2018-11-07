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
        return $response;
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
        return $response;
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
        return $response;
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
        return $response;
    }
}
