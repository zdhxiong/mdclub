<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 私信
 */
class Inbox extends ContainerAbstracts
{
    /**
     * 私信列表页
     *
     * @param  Request           $request
     * @param  Response          $response
     * @return ResponseInterface
     */
    public function pageIndex(Request $request, Response $response): ResponseInterface
    {
        return $this->view->render($response, '/inbox/index.php');
    }
}
