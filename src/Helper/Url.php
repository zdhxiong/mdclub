<?php

declare(strict_types=1);

namespace MDClub\Helper;

use MDClub\Constant\OptionConstant;
use MDClub\Facade\Library\Option;
use MDClub\Facade\Library\Request;
use MDClub\Initializer\App;

/**
 * URL 相关方法
 */
class Url
{
    /**
     * 获取网站根目录网址（含域名）
     *
     * @return string
     */
    public static function hostPath(): string
    {
        $uri = Request::getUri();

        return "{$uri->getScheme()}://{$uri->getHost()}";
    }

    /**
     * 获取静态资源的访问路径
     *
     * @return string
     */
    public static function staticPath(): string
    {
        $staticUrl = Option::get(OptionConstant::SITE_STATIC_URL);

        if ($staticUrl && substr($staticUrl, -1) !== '/') {
            $staticUrl .= '/';
        }

        if (!$staticUrl) {
            $staticUrl = self::hostPath() . '/static/';
        }

        return $staticUrl;
    }

    /**
     * 获取网站的根目录相对路径
     *
     * @return string
     */
    public static function rootPath(): string
    {
        $requestUri = parse_url('http://example.com' . $_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestScriptName = parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH);
        $requestScriptDir = dirname($requestScriptName);

        $basePath = '';
        if (stripos($requestUri, $requestScriptName) === 0) {
            $basePath = $requestScriptName;
        } elseif ($requestScriptDir !== '/' && stripos($requestUri, $requestScriptDir) === 0) {
            $basePath = $requestScriptDir;
        }

        return $basePath;
    }

    /**
     * 获取上传文件的访问路径
     *
     * @return string
     */
    public static function storagePath(): string
    {
        $storageUrl = Option::get(OptionConstant::STORAGE_URL);

        if ($storageUrl && substr($storageUrl, -1) !== '/') {
            $storageUrl .= '/';
        }

        if (!$storageUrl) {
            $storageUrl = self::hostPath() . '/static/upload/';
        }

        return $storageUrl;
    }

    /**
     * 从路由生成链接
     *
     * @param string $name
     * @param array  $data
     * @param array  $queryParams
     *
     * @return string
     */
    public static function fromRoute(string $name, array $data = [], array $queryParams = []): string
    {
        $routeParser = App::$slim->getRouteCollector()->getRouteParser();
        $host = Url::hostPath();

        return $host . $routeParser->urlFor($name, $data, $queryParams);
    }
}
