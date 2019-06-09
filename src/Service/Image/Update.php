<?php

declare(strict_types=1);

namespace App\Service\Image;

use App\Constant\ErrorConstant;
use App\Constant\UploadErrorConstant;
use App\Exception\ApiException;
use App\Helper\StringHelper;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 更新图片信息
 */
class Update extends Abstracts
{
    /**
     * 更新图片信息
     *
     * @param  string $hash 文件hash
     * @param  array  $data 更新的数据（仅允许更新 filename, item_type, item_id）
     */
    public function update(string $hash, array $data): void
    {
        $data = collect($data)->only(['filename', 'item_type', 'item_id'])->all();

        if (!$data) {
            return;
        }

        $this->model->where('hash', $hash)->update($data);
    }

    /**
     * 上传图片
     *
     * @param  int                   $userId 用户ID
     * @param  UploadedFileInterface $file   UploadedFile对象
     * @return string                        文件名（不含路径）
     */
    public function upload(int $userId, UploadedFileInterface $file): string
    {
        $uploadError = $this->validateImage($file);

        if ($uploadError) {
            throw new ApiException(ErrorConstant::SYSTEM_IMAGE_UPLOAD_FAILED, false, $uploadError);
        }

        $suffix = $this->getSuffix($file);
        $hash = StringHelper::guid() . '.' . $suffix;
        $timestamp = $this->requestService->time();
        $path = $this->getPath($hash, $timestamp);

        $this->storage->write($path, $file->getStream(), $this->getSize());

        [$imageWidth, $imageHeight] = getimagesize($file->getStream()->getMetadata('uri'));

        $this->model->insert([
            'hash'     => $hash,
            'filename' => $file->getClientFilename(),
            'width'    => $imageWidth,
            'height'   => $imageHeight,
            'user_id'  => $userId,
        ]);

        return $hash;
    }

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
            return UploadErrorConstant::getMessage()[$file->getError()];
        }

        if (!in_array($file->getClientMediaType(), ['image/jpeg', 'image/png', 'image/gif'])) {
            return '仅允许上传 jpg、png 或 gif 图片';
        }

        return false;
    }
}
