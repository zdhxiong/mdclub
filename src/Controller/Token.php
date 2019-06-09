<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Token
 */
class Token extends ContainerAbstracts
{
    /**
     * 创建 token，即登录
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        return $this->userLoginService
            ->fetchCollection()
            ->doLogin(
                $request->getParsedBodyParam('name'),
                $request->getParsedBodyParam('password'),
                $request->getParsedBodyParam('device')
            )->render($response);
    }
}
