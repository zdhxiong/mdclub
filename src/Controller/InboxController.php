<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 私信
 *
 * Class InboxController
 * @package App\Controller
 */
class InboxController extends ControllerAbstracts
{
    /**
     * 私信列表页
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
