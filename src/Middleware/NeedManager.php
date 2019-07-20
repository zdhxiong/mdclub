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
 * 路由中间件（需要管理员权限）
 *
 * @property-read Auth $auth
 */
class NeedManager extends Abstracts implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->auth->isManager()) {
            throw new ApiException(ApiError::USER_NEED_MANAGE_PERMISSION);
        }

        return $handler->handle($request);
    }
}
