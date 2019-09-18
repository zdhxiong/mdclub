<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use MDClub\Middleware\Trace;
use MDClub\Middleware\TrailingSlash;
use Slim\App;
use Slim\Middleware\ContentLengthMiddleware;
use Slim\Middleware\MethodOverrideMiddleware;

/**
 * 中间件
 */
class Middleware
{
    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $container = $app->getContainer();
        $debug = $container->get('settings')['debug'];

        // 在 Response 中添加 Trace 信息
        if ($debug) {
            $app->add(new Trace($container));
        }

        // header 中添加 Content-Length
        $app->add(new ContentLengthMiddleware());

        // Http Cache todo 该中间件不兼容 psr15
        // $app->add(new HttpCache('public', 86400));

        // 将以斜杠结尾的路径跳转到不以斜杠结尾的路径
        $app->add(new TrailingSlash());

        // 路由中间件必须在错误处理中间件前面
        $app->addRoutingMiddleware();

        // 方法重写，必须位于路由中间件后面
        $app->add(new MethodOverrideMiddleware());
    }
}
