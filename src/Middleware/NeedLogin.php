<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Library\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 路由中间件（需要登录权限）
 *
 * @property-read Auth $auth
 */
class NeedLogin extends Abstracts implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->auth->userId()) {
            throw new ApiException(ApiError::USER_TOKEN_FAILED);
        }

        return $handler->handle($request);
    }
}
