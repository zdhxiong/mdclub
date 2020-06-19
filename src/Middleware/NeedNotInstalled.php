<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use MDClub\Constant\RouteNameConstant;
use MDClub\Initializer\App;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Factory\ResponseFactory;

/**
 * 需要未安装
 */
class NeedNotInstalled implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!file_exists(__DIR__ . '/../../config/config.php')) {
            return $handler->handle($request);
        }

        $response = (new ResponseFactory())->createResponse();
        $routeParser = App::$slim->getRouteCollector()->getRouteParser();

        return $response
            ->withHeader('Location', $routeParser->urlFor(RouteNameConstant::INDEX))
            ->withStatus(301);
    }
}
