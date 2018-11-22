<?php

declare(strict_types=1);

namespace App\Traits;

/**
 * URL 相关方法
 *
 * Trait UrlTraits
 * @package App\Traits
 */
trait UrlTraits
{
    /**
     * 获取上传文件的访问路径
     *
     * @return string
     */
    protected function getStorageUrl(): string
    {
        $storageUrl = $this->optionService->get('storage_url');
        if ($storageUrl && substr($storageUrl, -1) !== '/') {
            $storageUrl .= '/';
        }

        if (!$storageUrl) {
            $storageUrl = $this->getSiteUrl() . '/static/upload/';
        }

        return $storageUrl;
    }

    /**
     * 获取静态资源的访问路径
     *
     * @return string
     */
    protected function getStaticUrl(): string
    {
        $staticUrl = $this->optionService->get('site_static_url');
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
    protected function getRootUrl(): string
    {
        return $this->request->getUri()->getBasePath();
    }

    /**
     * 获取网站根目录网址（含域名）
     *
     * @return string
     */
    protected function getSiteUrl(): string
    {
        $uri = $this->request->getUri();

        return $uri->getScheme() . '://' . $uri->getHost();
    }
}
