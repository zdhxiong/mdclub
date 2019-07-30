<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use MDClub\Route\Admin;
use MDClub\Route\Home;
use MDClub\Route\RestApi;
use MDClub\Route\Rss;
use Slim\App;

/**
 * 路由
 */
class Route
{
    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $container = $app->getContainer();
        $routeCollector = $app->getRouteCollector();
        $debug = $container->get('settings')['debug'];
        $runtime = $container->get('settings')['runtime'];

        // 生产模式启用路由缓存
        if (!$debug) {
            $routeCollector->setCacheFile($runtime . '/CompiledRoute.php');
        }

        // 设置路由回调策略
        $routeCollector->setDefaultInvocationStrategy(new RouteStrategy($container));

        new Home($app);
        new RestApi($app);
        new Rss($app);
        new Admin($app);
    }
}
