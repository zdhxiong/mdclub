<?php

declare(strict_types=1);

namespace MDClub\Controller\Install;

use MDClub\Constant\RouteNameConstant;
use MDClub\Facade\Library\View;
use MDClub\Initializer\App;
use Psr\Http\Message\ResponseInterface;

/**
 * 安装界面首页
 */
class Index
{
    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        // 存在 config.php 文件，表示已安装完成
        if (file_exists(__DIR__ . '/../../../config.php')) {
            /** @var ResponseInterface $response */
            $response = App::$container->get(ResponseInterface::class);

            $routeParser = App::$slim->getRouteCollector()->getRouteParser();

            return $response
                ->withHeader('Location', $routeParser->urlFor(RouteNameConstant::INDEX))
                ->withStatus(301);
        }

        return View::render('/install.php');
    }
}
