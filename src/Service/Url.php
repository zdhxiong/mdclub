<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ContainerAbstracts;

/**
 * 链接生成
 */
class Url extends ContainerAbstracts
{
    /**
     * 获取网站根目录网址（含域名）
     *
     * @return string
     */
    public function site(): string
    {
        $uri = $this->request->getUri();

        return $uri->getScheme() . '://' . $uri->getHost();
    }

    /**
     * 获取静态资源的访问路径
     *
     * @return string
     */
    public function static(): string
    {
        $staticUrl = $this->optionService->site_static_url;

        if ($staticUrl && substr($staticUrl, -1) !== '/') {
            $staticUrl .= '/';
        }

        if (!$staticUrl) {
            $staticUrl = $this->site() . '/static/';
        }

        return $staticUrl;
    }

    /**
     * 获取网站的根目录相对路径
     *
     * @return string
     */
    public function root(): string
    {
        /** @var \Slim\Http\Uri $uri */
        $uri = $this->request->getUri();

        return $uri->getBasePath();
    }

    /**
     * 获取上传文件的访问路径
     *
     * @return string
     */
    public function storage(): string
    {
        $storageUrl = $this->optionService->storage_url;

        if ($storageUrl && substr($storageUrl, -1) !== '/') {
            $storageUrl .= '/';
        }

        if (!$storageUrl) {
            $storageUrl = $this->site() . '/static/upload/';
        }

        return $storageUrl;
    }
}
