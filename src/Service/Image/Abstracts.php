<?php

declare(strict_types=1);

namespace App\Service\Image;

use App\Abstracts\ContainerAbstracts;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 图片抽象类
 *
 * 如果使用本地存储，则每张图片需要保存为：
 * 1. 原图
 * 2. 宽度为 650px，高度自适应（存储为本地图片时，以 _r 为后缀）
 * 3. 宽度为 132px，高度为 88px，居中裁剪的缩略图（存储为本地图片时，以 _t 为后缀）
 */
abstract class Abstracts extends ContainerAbstracts
{
    /**
     * @var \App\Model\Image
     */
    protected $model;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->imageModel;
    }

    /**
     * 图片尺寸
     *
     * @return array
     */
    protected function getSize(): array
    {
        return [
            't' => [132, 88],
            'r' => [650, 0],
        ];
    }

    /**
     * 获取图片存储的相对路径
     *
     * @param  string $hash
     * @param  int    $timestamp
     * @return string
     */
    protected function getPath(string $hash, int $timestamp): string
    {
        $path = implode('/', ['image', date('Y-m/d', $timestamp), substr($hash, 0, 2)]);

        return "{$path}/{$hash}";
    }

    /**
     * 获取图片后缀名
     *
     * @param  UploadedFileInterface $file
     * @return string
     */
    protected function getSuffix(UploadedFileInterface $file): string
    {
        switch ($file->getClientMediaType()) {
            case 'image/gif':
                $suffix = 'gif';
                break;
            case 'image/png':
                $suffix = 'png';
                break;
            default:
                $suffix = 'jpg';
                break;
        }

        return $suffix;
    }

    /**
     * 获取各种尺寸的图片访问路径
     *
     * @param  string $hash
     * @param  int    $timestamp
     * @return array
     */
    protected function getUrls(string $hash, int $timestamp): array
    {
        $path = $this->getPath($hash, $timestamp);
        $thumbs = $this->getSize();

        return $this->storage->get($path, $thumbs);
    }
}
