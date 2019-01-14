<?php

declare(strict_types=1);

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 添加支持跨域请求的响应头
 *
 * Class EnableCrossRequest
 * @package App\Middleware
 */
class EnableCrossRequest
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        /** @var Response $response */
        $response = $next($request, $response);

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Token, Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie')
            ->withHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PATCH, DELETE');
    }
}
