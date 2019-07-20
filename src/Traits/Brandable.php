<?php

declare(strict_types=1);

namespace MDClub\Traits;

use MDClub\Constant\UploadError;
use MDClub\Helper\Guid;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 对象的标识图。包括 user-avatar, user-cover, topic-cover。
 *
 * 只允许使用 jpg 和 png 格式
 *
 * @property-read \MDClub\Library\Storage $storage
 */
trait Brandable
{
    /**
     * 图片类型，包括 user-avatar, user-cover, topic-cover
     *
     * @return string
     */
    abstract protected function getBrandType(): string;

    /**
     * 图片尺寸数组
     *
     * @return array
     */
    abstract protected function getBrandSize(): array;

    /**
     * 获取默认图片地址
     *
     * @return array
     */
    abstract protected function getDefaultBrandUrls(): array;

    /**
     * 获取文件存储的相对路径
     *
     * @param  int    $id
     * @param  string $filename
     * @return string
     */
    protected function getBrandPath(int $id, string $filename): string
    {
        $hash = md5((string)$id);
        $path = implode('/', [$this->getBrandType(), substr($hash, 0, 2), substr($hash, 2, 2)]);

        return "{$path}/{$filename}";
    }

    /**
     * 获取各种尺寸文件的访问路径数组
     *
     * @param  int    $id
     * @param  string $filename
     * @return array
     */
    public function getBrandUrls(int $id, string $filename = null): array
    {
        if (!$filename) {
            return $this->getDefaultBrandUrls();
        }

        $path = $this->getBrandPath($id, $filename);
        $thumbs = $this->getBrandSize();

        return $this->storage->get($path, $thumbs);
    }

    /**
     * 删除图片
     *
     * @param  int    $id
     * @param  string $filename
     */
    public function deleteImage(int $id, string $filename): void
    {
        $path = $this->getBrandPath($id, $filename);
        $thumbs = $this->getBrandSize();

        $this->storage->delete($path, $thumbs);
    }

    /**
     * 上传图片，上传前需要调用 validateImage 验证图片
     *
     * @param  int                   $id
     * @param  UploadedFileInterface $file UploadedFile对象
     * @return string                      文件名（不含路径）
     */
    public function uploadImage(int $id, UploadedFileInterface $file): string
    {
        $token = Guid::generate();
        $suffix = $file->getClientMediaType() === 'image/png' ? 'png' : 'jpg';
        $filename = "{$token}.{$suffix}";
        $path = $this->getBrandPath($id, $filename);
        $thumbs = $this->getBrandSize();

        $this->storage->write($path, $file->getStream(), $thumbs);

        return $filename;
    }

    /**
     * 对上传的文件进行验证
     *
     * @param  UploadedFileInterface $file
     * @return bool|string                 错误描述，false表示没有错误
     */
    protected function validateImage(UploadedFileInterface $file)
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            return UploadError::getMessage()[$file->getError()];
        }

        if (!in_array($file->getClientMediaType(), ['image/jpeg', 'image/png'])) {
            return '仅允许上传 jpg 或 png 图片';
        }

        return false;
    }
}
