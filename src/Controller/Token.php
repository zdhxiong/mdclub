<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Token
 *
 * Class TokenController
 * @package App\Controller
 */
class Token extends ControllerAbstracts
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
        $userInfo = $this->container->userLoginService->doLogin(
            $request->getParsedBodyParam('name'),
            $request->getParsedBodyParam('password'),
            $request->getParsedBodyParam('device')
        );

        return $this->success($response, $userInfo);
    }
}
