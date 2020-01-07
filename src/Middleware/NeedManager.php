<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 需要管理员权限
 */
class NeedManager implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!Auth::userId()) {
            throw new ApiException(ApiErrorConstant::USER_NEED_LOGIN);
        }

        if (Auth::isNotManager()) {
            throw new ApiException(ApiErrorConstant::USER_NEED_MANAGE_PERMISSION);
        }

        return $handler->handle($request);
    }
}
