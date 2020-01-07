<?php

declare(strict_types=1);

namespace MDClub\Controller\Admin;

use MDClub\Exception\SystemException;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\View;
use MDClub\Facade\Service\UserService;
use MDClub\Helper\Url;
use Psr\Http\Message\ResponseInterface;

/**
 * 管理后台首页
 */
class Index
{
    /**
     * 后台管理控制台页面
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $staticUrl = Url::staticPath();
        $webpackAssetsPath = "{$staticUrl}admin/webpack-assets.json";

        if (!$assetsInfo = file_get_contents($webpackAssetsPath)) {
            throw new SystemException("Can not find file: {$webpackAssetsPath}");
        }

        collect(json_decode($assetsInfo, true))
            ->flatten()
            ->each(
                static function ($item) use ($staticUrl, &$css, &$js) {
                    if ($item === 'bundle.js') {
                        $js .= "<script src='http://localhost:8080/${item}'></script>";

                        return;
                    }

                    $assets = "${staticUrl}admin/${item}";

                    if (strpos($item, 'css') === 0) {
                        $css .= "<link rel='stylesheet' href='${assets}'/>";
                    } elseif (strpos($item, 'js') === 0) {
                        $js .= "<script src='${assets}'></script>";
                    }
                }
            );

        return View::render(
            '/admin.php',
            [
                'css' => $css,
                'js' => $js,
                'root' => Url::rootPath(),
                'host' => Url::hostPath(),
                'user' => UserService::get(Auth::userId()),
            ]
        );
    }
}
