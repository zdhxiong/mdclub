<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use RingCentral\Psr7\Response;
use Slim\Psr7\Factory\UriFactory;
use MDClub\Initializer\App;

/**
 * 函数入口
 *
 * @param  ServerRequestInterface $request https://github.com/reactphp/http/blob/master/src/Io/ServerRequest.php
 * @param  array                  $context
 * @return Response                        https://github.com/ringcentral/psr7/blob/master/src/Response.php
 */
function handler(ServerRequestInterface $request, array $context): Response {
    require __DIR__ . '/vendor/autoload.php';

    // 默认的 uri 有错误，这里重新处理
    $query = collect($request->getQueryParams())
        ->map(function ($value, $key) {
            return "${key}=${value}";
        })
        ->implode('&');

    $url = $request->getAttribute('path') . ($query ? "?${query}" : '');
    $uri = (new UriFactory())->createUri($url);

    $request = $request->withUri($uri);

    $app = new App($request);
    $response = $app->run();

    return new Response(200, $response->getHeaders(), $response->getBody());
}
