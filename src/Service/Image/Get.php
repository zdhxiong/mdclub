<?php

declare(strict_types=1);

namespace MDClub\Service\Image;

use MDClub\Traits\Getable;

/**
 * 获取图片
 *
 * @property-read \MDClub\Model\Image $model
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 获取各种尺寸的图片访问路径
     *
     * @param  string $key
     * @param  int    $timestamp
     * @return array
     */
    public function getUrls(string $key, int $timestamp): array
    {
        $path = $this->getPath($key, $timestamp);
        $thumbs = $this->getSize();

        return $this->storage->get($path, $thumbs);
    }

    /**
     * 获取图片列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }
}
