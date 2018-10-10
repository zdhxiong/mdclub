<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 后台管理
 *
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends Controller
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
        if (!$this->roleService->managerId()) {
            return $this->view->render($response, '/404.php');
        } else {
            $response->getBody()->write('
<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0, user-scalable=no"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp"/>
    <link rel="stylesheet" href="__STATIC__/dist/css/vendor.91f778a52bc3dbf4bf7ba981dc28851f.css"/>
    <link rel="stylesheet" href="__STATIC__/dist/css/app.fb316f03ff48fb8d8c3d6033cccf2aab.css"/>
</head>
<body class="mdui-drawer-body-left mdui-appbar-with-toolbar mdui-theme-primary-indigo mdui-theme-accent-pink">
    <div id="app"></div>
    <script src="__STATIC__/dist/js/manifest.83238df4f015d431b39d.js"></script>
    <script src="__STATIC__/dist/js/vendor.3ad33d5f41e0c7f9fe8c.js"></script>
    <script src="__STATIC__/dist/js/app.2a07862ef0f88f8ed13a.js"></script>
</body>
</html>');

            return $response;
        }
    }
}
