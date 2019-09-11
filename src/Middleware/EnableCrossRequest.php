<?php

declare(strict_types=1);

namespace MDClub\Middleware;

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

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PATCH, PUT, DELETE')
            ->withHeader(
                'Access-Control-Allow-Headers',
                'Token, Origin, X-Requested-With, Accept, Content-Type, Connection, User-Agent'
            );
    }
}
