<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use App\Exception\SystemException;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 后台管理
 */
class Admin extends ContainerAbstracts
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
        if (!$userId = $this->roleService->managerId()) {
            return $this->view->render($response, '/404.php');
        }

        $siteUrl = $this->urlService->site();
        $staticUrl = $this->urlService->static();
        $rootUrl = $this->urlService->root();

        if (!$assetsInfo = file_get_contents($staticUrl . 'admin/webpack-assets.json')) {
            throw new SystemException('无法访问 ' . $staticUrl . 'admin/webpack-assets.json 文件');
        }

        $assetsInfo = json_decode($assetsInfo, true);
        $css = $js = [];

        collect($assetsInfo)->flatten()->each(static function ($item) use ($staticUrl, &$css, &$js) {
            if ($item === 'bundle.js') {
                $js[] = "<script src='http://localhost:8080/${item}'></script>";
            } elseif (strpos($item, 'css') === 0) {
                $css[] = "<link rel='stylesheet' href='${staticUrl}admin/${item}'/>";
            } elseif (strpos($item, 'js') === 0) {
                $js[] = "<script src='${staticUrl}admin/${item}'></script>";
            }
        });

        $cssString = implode('', $css);
        $jsString = implode('', $js);

        $userInfo = $this->userGetService
            ->fetchCollection()
            ->get($userId, true)
            ->toJson();

        $response->getBody()->write(<<<END
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <title></title>
    $cssString
</head>
<body class="mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-blue mdui-theme-accent-blue">
    <script>
        window.G_API = "$rootUrl/api"; // api 地址
        window.G_ROOT = "$rootUrl"; // 网站根目录相对路径
        window.G_ADMIN_ROOT = "$rootUrl/admin"; // 网站后台根目录相对路径
        window.G_SITE = "$siteUrl"; // 网址（含域名）
        window.G_USER = $userInfo;
    </script>
    $jsString
</body>
</html>
END
        );

        return $response;
    }
}
