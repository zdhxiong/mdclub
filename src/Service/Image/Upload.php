<?php

declare(strict_types=1);

namespace MDClub\Service\Image;

use MDClub\Constant\ApiError;
use MDClub\Constant\UploadError;
use MDClub\Exception\ApiException;
use MDClub\Helper\Guid;
use MDClub\Helper\Request;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 图片上传
 */
class Upload extends Abstracts
{
    /**
     * 对上传的文件进行验证
     *
     * @param  UploadedFileInterface $file
     * @return bool|string                 错误描述，false表示没有错误
     */
    protected function validateImage(UploadedFileInterface $file)
    {
        if ($file === null) {
            return '请选择要上传的图片';
        }

        if ($file->getError() !== UPLOAD_ERR_OK) {
            return UploadError::getMessage()[$file->getError()];
        }

        if (!in_array($file->getClientMediaType(), ['image/jpeg', 'image/png', 'image/gif'])) {
            return '仅允许上传 jpg、png 或 gif 图片';
        }

        return false;
    }

    /**
     * 上传图片
     *
     * @param  UploadedFileInterface $file   UploadedFile对象
     * @return string                        文件名（不含路径）
     */
    public function upload(UploadedFileInterface $file): string
    {
        $uploadError = $this->validateImage($file);

        if ($uploadError) {
            throw new ApiException(ApiError::SYSTEM_IMAGE_UPLOAD_FAILED, false, $uploadError);
        }

        $userId = $this->auth->userId();
        $suffix = $this->getSuffix($file);
        $key = Guid::generate() . '.' . $suffix;
        $timestamp = Request::time($this->request);
        $path = $this->getPath($key, $timestamp);

        $this->storage->write($path, $file->getStream(), $this->getSize());

        [$imageWidth, $imageHeight] = getimagesize($file->getStream()->getMetadata('uri'));

        $this->model->insert([
            'key'      => $key,
            'filename' => $file->getClientFilename(),
            'width'    => $imageWidth,
            'height'   => $imageHeight,
            'user_id'  => $userId,
        ]);

        return $key;
    }
}
