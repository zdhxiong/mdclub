<?php

declare(strict_types=1);

namespace MDClub\Helper;

use Psr\Http\Message\ResponseInterface;

/**
 * 响应相关函数
 */
class Response
{
    /**
     * 支持跨域
     *
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    public static function withCors(ResponseInterface $response): ResponseInterface
    {
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PATCH, PUT, DELETE')
            ->withHeader(
                'Access-Control-Allow-Headers',
                'Token, Origin, X-Requested-With, Accept, Content-Type, Connection, User-Agent'
            );
    }
}
