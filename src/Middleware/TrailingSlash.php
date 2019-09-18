<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

/**
 * 如果请求 URL 以 / 结尾，则跳转到不带 / 的 URL
 *
 * @link http://www.slimframework.com/docs/v4/cookbook/route-patterns.html
 */
class TrailingSlash implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();

        if ($path !== '/' && substr($path, -1) === '/') {
            $uri = $uri->withPath(substr($path, 0, -1));

            if ($request->getMethod() === 'GET') {
                $response = new Response();

                return $response
                    ->withHeader('Location', (string) $uri)
                    ->withStatus(301);
            } else {
                $request = $request->withUri($uri);
            }
        }

        return $handler->handle($request);
    }
}
