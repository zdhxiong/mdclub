<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Exception\ValidationException;
use MDClub\Facade\Service\ImageService;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 图片验证
 */
class Image extends Abstracts
{
    /**
     * 上传文件前进行验证
     *
     * @param UploadedFileInterface[] $images
     *
     * @return UploadedFileInterface
     */
    public function upload(array $images): UploadedFileInterface
    {
        try {
            $data = $this->data($images)
                ->field('image')->exist()->uploadedImage()
                ->validate();
        } catch (ValidationException $e) {
            throw new ApiException(ApiErrorConstant::COMMON_IMAGE_UPLOAD_FAILED, false, $e->getErrors()['avatar']);
        }

        return $data['image'];
    }

    /**
     * 更新图片信息前的验证
     *
     * @param string $key
     * @param array  $data [filename]
     *
     * @return array
     */
    public function update(string $key, array $data): array
    {
        ImageService::getOrFail($key);

        return $this->data($data)
            ->field('filename')->stripTags()->trim()->notEmpty()->length(1, 255)->htmlentities()
            ->validate();
    }
}
