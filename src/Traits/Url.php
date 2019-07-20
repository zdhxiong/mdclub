<?php

declare(strict_types=1);

namespace MDClub\Traits;

use Psr\Http\Message\ServerRequestInterface;

/**
 * URL
 *
 * @property-read ServerRequestInterface  $request
 * @property-read \MDClub\Library\Option  $option
 */
trait Url
{
    /**
     * 获取网站根目录网址（含域名）
     *
     * @return string
     */
    public function getSiteUrl(): string
    {
        $uri = $this->request->getUri();

        return $uri->getScheme() . '://' . $uri->getHost();
    }

    /**
     * 获取静态资源的访问路径
     *
     * @return string
     */
    public function getStaticUrl(): string
    {
        $staticUrl = $this->option->site_static_url;

        if ($staticUrl && substr($staticUrl, -1) !== '/') {
            $staticUrl .= '/';
        }

        if (!$staticUrl) {
            $staticUrl = $this->getSiteUrl() . '/static/';
        }

        return $staticUrl;
    }

    /**
     * 获取网站的根目录相对路径
     *
     * @return string
     */
    public function getRootUrl(): string
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
    public function getStorageUrl(): string
    {
        $storageUrl = $this->option->storage_url;

        if ($storageUrl && substr($storageUrl, -1) !== '/') {
            $storageUrl .= '/';
        }

        if (!$storageUrl) {
            $storageUrl = $this->getSiteUrl() . '/static/upload/';
        }

        return $storageUrl;
    }
}
