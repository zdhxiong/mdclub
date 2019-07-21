<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use RingCentral\Psr7\Response;
use Slim\Psr7\Factory\UriFactory;
use MDClub\Initializer\App;

/**
 * 获取函数计算的配置参数
 *
 * @return array
 */
function getConfig(): array
{
    $config = require __DIR__ . '/deploy/aliyun_fc/config.default.php';

    if (is_file(__DIR__ . '/deploy/aliyun_fc/config.php')) {
        $config = array_merge($config, require __DIR__ . '/deploy/aliyun_fc/config.php');
    }

    return $config;
}

/**
 * 对 request 做处理
 *
 * @param  ServerRequestInterface $request
 * @return ServerRequestInterface
 */
function handleRequest(ServerRequestInterface $request): ServerRequestInterface
{
    // 默认的 uri 有错误，这里重新处理
    $query = collect($request->getQueryParams())
        ->map(function ($value, $key) {
            return "${key}=${value}";
        })
        ->implode('&');

    $url = $request->getAttribute('path') . ($query ? "?${query}" : '');
    $uri = (new UriFactory())->createUri($url);

    $request = $request->withUri($uri);

    return $request;
}

/**
 * 函数入口
 *
 * @param  ServerRequestInterface $request https://github.com/reactphp/http/blob/master/src/Io/ServerRequest.php
 * @param  array                  $context
 * @return Response                        https://github.com/ringcentral/psr7/blob/master/src/Response.php
 */
function handler(ServerRequestInterface $request, array $context): Response {
    require __DIR__ . '/vendor/autoload.php';

    $request = handleRequest($request);
    $config = getConfig();

    $app = new App($request, $config);
    $response = $app->run();

    return new Response(200, $response->getHeaders(), $response->getBody());
}
