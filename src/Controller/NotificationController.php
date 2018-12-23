<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use Psr\Http\Message\ResponseInterface;
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
     * @param  Request           $request
     * @param  Response          $response
     * @return ResponseInterface
     */
    public function pageIndex(Request $request, Response $response): ResponseInterface
    {
        return $this->view->render($response, '/notification/index.php');
    }
}
