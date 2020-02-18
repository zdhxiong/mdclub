<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Library\Storage;
use MDClub\Facade\Model\ImageModel;
use MDClub\Facade\Validator\ImageValidator;
use MDClub\Helper\Str;
use MDClub\Service\Interfaces\GetableInterface;
use MDClub\Service\Traits\Getable;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 图片服务
 *
 * 如果使用本地存储，则每张图片需要保存为：
 * 1. 原图
 * 2. 宽度为 650px，高度自适应（存储为本地图片时，以 _release 为后缀）
 * 3. 宽度为 132px，高度为 88px，居中裁剪的缩略图（存储为本地图片时，以 _thumb 为后缀）
 */
class Image extends Abstracts implements GetableInterface
{
    use Getable;

    /**
     * @inheritDoc
     */
    protected function getModel(): string
    {
        return \MDClub\Model\Image::class;
    }

    /**
     * 图片尺寸
     *
     * @return array
     */
    protected function getSize(): array
    {
        return [
            'thumb' => [132, 88],
            'release' => [650, 0],
        ];
    }

    /**
     * 获取图片存储的相对路径
     *
     * @param string $key
     * @param int    $timestamp
     *
     * @return string
     */
    protected function getPath(string $key, int $timestamp): string
    {
        $path = implode('/', ['image', date('Y-m/d', $timestamp), substr($key, 0, 2)]);

        return "/{$path}/{$key}";
    }

    /**
     * 获取图片后缀名
     *
     * @param UploadedFileInterface $file
     *
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
     * @param string $key
     * @param int    $timestamp
     *
     * @return array
     */
    public function getUrls(string $key, int $timestamp): array
    {
        $path = $this->getPath($key, $timestamp);
        $thumbs = $this->getSize();

        return Storage::get($path, $thumbs);
    }

    /**
     * 上传图片
     *
     * @param UploadedFileInterface[] $images
     *
     * @return string 文件名（不含路径）
     */
    public function upload(array $images): string
    {
        $file = ImageValidator::upload($images);
        $key = Str::guid() . '.' . $this->getSuffix($file);
        $path = $this->getPath($key, Request::time());

        Storage::write($path, $file->getStream(), $this->getSize());

        [$width, $height] = getimagesize($file->getStream()->getMetadata('uri'));

        ImageModel
            ::set('key', $key)
            ->set('filename', $file->getClientFilename())
            ->set('width', $width)
            ->set('height', $height)
            ->set('user_id', Auth::userId())
            ->insert();

        return $key;
    }

    /**
     * 更新图片信息
     *
     * @param string $key
     * @param array  $data [filename]
     */
    public function update(string $key, array $data): void
    {
        $data = ImageValidator::update($key, $data);

        if (!$data) {
            return;
        }

        ImageModel
            ::where('key', $key)
            ->update('filename', $data['filename']);
    }

    /**
     * 执行删除图片，不验证图片是否存在
     *
     * @param array $images
     */
    public function doDelete(array $images): void
    {
        $keys = collect($images)->pluck('key');

        ImageModel::delete($keys);

        foreach ($images as $image) {
            Storage::delete(
                $this->getPath($image['key'], $image['create_time']),
                $this->getSize()
            );
        }
    }

    /**
     * 批量删除图片
     *
     * @param array $keys
     */
    public function deleteMultiple(array $keys): void
    {
        $existImages = ImageModel::field(['key', 'create_time'])->select($keys);

        if (!$existImages) {
            return;
        }

        $this->doDelete($existImages);
    }

    /**
     * 删除图片
     *
     * @param string $key
     */
    public function delete(string $key): void
    {
        $image = $this->getOrFail($key);

        $this->doDelete([$image]);
    }
}
