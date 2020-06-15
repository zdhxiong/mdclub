<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use MDClub\Middleware\Trace;
use MDClub\Middleware\TrailingSlash;
use MDClub\Route\Admin;
use MDClub\Route\Home;
use MDClub\Route\Install;
use MDClub\Route\RestApi;
use MDClub\Route\Rss;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Middleware\MethodOverrideMiddleware;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 路由
 */
class Route
{
    public function __construct()
    {
        $slim = App::$slim;
        $config = App::$config;
        $routeCollector = $slim->getRouteCollector();

        // 添加中间件
        if ($config['APP_DEBUG']) {
            $slim->add(new Trace());
        }

        $slim->add(new ContentLengthMiddleware());
        $slim->add(new TrailingSlash());
        $slim->addRoutingMiddleware();
        $slim->add(new MethodOverrideMiddleware());

        // 生产模式启用路由缓存
        if (!$config['APP_DEBUG']) {
            // 确保临时文件目录存在（Fast-Route 不会自动创建缓存文件目录）
            if (!is_dir($config['APP_RUNTIME'])) {
                (new Filesystem())->mkdir($config['APP_RUNTIME']);
            }

            $routeCollector->setCacheFile($config['APP_RUNTIME'] . '/CompiledRoute.php');
        }

        // 设置路由回调策略
        $routeCollector->setDefaultInvocationStrategy(new RouteStrategy());

        new Home();
        new RestApi();
        new Rss();
        new Admin();
        new Install();

        $slim->map(
            ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
            '/{routes:.+}',
            function () {
                throw new HttpNotFoundException(App::$container->get(ServerRequestInterface::class));
            }
        );
    }
}
