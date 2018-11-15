<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 通知
 *
 * Class NotificationController
 * @package App\Controller
 */
class NotificationController extends ControllerAbstracts
{
    /**
     * 通知列表页
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function pageIndex(Request $request, Response $response): Response
    {
        return $response;
    }
}
