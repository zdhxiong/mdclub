<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Getable;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 图片
 *
 * 如果使用本地存储，则每张图片需要保存为：
 * 1. 原图
 * 2. 宽度为 650px，高度自适应（存储为本地图片时，以 _r 为后缀）
 * 3. 宽度为 132px，高度为 88px，居中裁剪的缩略图（存储为本地图片时，以 _t 为后缀）
 */
class Image extends ImageAbstract
{
    use Getable;

    /**
     * @var \MDClub\Model\Image
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->model = $this->imageModel;
    }

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
