<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 后台管理
 *
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends ControllerAbstracts
{
    /**
     * 后台管理控制台页面
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ResponseInterface
     */
    public function pageIndex(Request $request, Response $response): ResponseInterface
    {
        $userId = $this->roleService->managerId();

        if (!$userId) {
            // 非管理员登录禁止访问
            return $this->view->render($response, '/404.php');
        }

        $staticUrl = $this->getStaticUrl();
        $rootUrl = $this->getRootUrl();
        $assetsInfo = file_get_contents($staticUrl . 'admin/webpack-assets.json');
        if (!$assetsInfo) {
            throw new \Exception('无法访问 ' . $staticUrl . 'admin/webpack-assets.json 文件');
        }

        $assetsInfo = json_decode($assetsInfo, true);
        $css = [];
        $js = [];

        foreach ($assetsInfo as $files) {
            foreach ($files as $suffix => $file) {
                if ($suffix == 'css') {
                    $css[] = '<link rel="stylesheet" href="' . $staticUrl . 'admin/' . $file . '"/>';
                }

                if ($suffix == 'js') {
                    if ($file == 'bundle.js') {
                        $js[] = '<script src="http://localhost:8080/' . $file . '"></script>';
                    } else {
                        $js[] = '<script src="' . $staticUrl . 'admin/' . $file . '"></script>';
                    }
                }
            }
        }

        $response->getBody()->write('
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    ' . implode('', $css) . '
</head>
<body class="mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-indigo mdui-theme-accent-pink">
    <div id="app"></div>
    <script>
        window.G_API = "' . $rootUrl . '/api";
        window.G_ROOT = "' . $rootUrl . '";
    </script>
    ' . implode('', $js) . '
</body>
</html>');

        return $response;
    }
}
