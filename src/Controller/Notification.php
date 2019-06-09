<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 通知
 */
class Notification extends ContainerAbstracts
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
