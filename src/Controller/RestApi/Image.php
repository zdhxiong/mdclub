<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\Abstracts;
use MDClub\Helper\Request;
use MDClub\Middleware\NeedManager;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 图片
 */
class Image extends Abstracts
{
    /**
     * 获取图片列表
     *
     * @uses NeedManager
     * @return array
     */
    public function getList(): array
    {
        return $this->imageGetService->getList();
    }

    /**
     * 上传图片
     *
     * @return array
     */
    public function upload(): array
    {
        /** @var UploadedFileInterface $file */
        $file = $this->request->getUploadedFiles()['image'] ?? null;

        $key = $this->imageUploadService->upload($file);

        return $this->imageGetService->get($key);
    }

    /**
     * 批量删除图片
     *
     * @uses NeedManager
     * @return array
     */
    public function deleteMultiple(): array
    {
        $keys = Request::getQueryParamToArray($this->request, 'key', 40);

        $this->imageDeleteService->deleteMultiple($keys);

        return [];
    }

    /**
     * 获取图片信息
     *
     * @param  string  $key
     * @return array
     */
    public function get(string $key): array
    {
        return $this->imageGetService->get($key);
    }

    /**
     * 更新图片信息
     *
     * @param  string   $key
     * @return array
     * @uses NeedManager
     */
    public function update(string $key): array
    {
        $filename = $this->request->getParsedBody()['filename'] ?? '';
        $this->imageUpdateService->update($key, ['filename' => $filename]);

        return $this->imageGetService->get($key);
    }

    /**
     * 删除图片
     *
     * @param  string   $key
     * @return array
     * @uses NeedManager
     */
    public function delete(string $key): array
    {
        $this->imageDeleteService->delete($key);

        return [];
    }
}
