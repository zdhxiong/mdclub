<?php

declare(strict_types=1);

namespace App\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 如果请求 URL 以 / 结尾，则跳转到不带 / 的 URL
 *
 * @link https://www.slimframework.com/docs/v3/cookbook/route-patterns.html
 *
 * Class TrailingSlash
 * @package App\Middleware
 */
class TrailingSlash
{
    public function __invoke(Request $request, Response $response, callable $next)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path != '/' && substr($path, -1) == '/') {
            $uri = $uri->withPath(substr($path, 0, -1));

            if ($request->isGet()) {
                return $response->withRedirect((string)$uri, 301);
            } else {
                return $next($request->withUri($uri), $response);
            }
        }

        return $next($request, $response);
    }
}
