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
 * 从 HTTP 触发器获取请求对象
 *
 * @param  ServerRequestInterface $request
 * @return ServerRequestInterface
 */
function getRequestFromHttpTrigger(ServerRequestInterface $request): ServerRequestInterface
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
 * 从 API 网关触发器创建请求对象
 *
 * @param  string                 $event
 * @return ServerRequestInterface
 */
function getRequestFromApiGatewayTrigger(string $event): ServerRequestInterface
{

}

/**
 * 函数入口
 *
 * @param  ServerRequestInterface|string $event
 *
 * 使用 HTTP 触发器时，参数为实现了 ServerRequestInterface 的实例，使用的库为：
 * https://github.com/reactphp/http/blob/master/src/Io/ServerRequest.php
 *
 * 使用 API 网关触发器时，参数为字符串
 *
 * @param  array                  $context
 * @return Response                        https://github.com/ringcentral/psr7/blob/master/src/Response.php
 */
function handler($event, array $context): Response {
    require __DIR__ . '/vendor/autoload.php';

    if ($event instanceof ServerRequestInterface) {
        $request = getRequestFromHttpTrigger($event);
    } else {
        $request = getRequestFromApiGatewayTrigger($event);
    }

    $config = getConfig();

    $app = new App($request, $config);
    $response = $app->run();

    return new Response(200, $response->getHeaders(), $response->getBody());
}
