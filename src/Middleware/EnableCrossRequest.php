<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use MDClub\Helper\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 添加支持跨域请求的响应头
 */
class EnableCrossRequest implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request);

        return Response::withCors($response);
    }
}
