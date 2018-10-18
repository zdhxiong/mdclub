<?php

declare(strict_types=1);

namespace App\Service;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 用户封面管理
 *
 * Class UserCoverService
 * @package App\Service
 */
class UserCoverService extends BrandImageService
{
    /**
     * @var string 图片类型
     */
    protected $imageType = 'user-cover';

    /**
     * @var array 图片尺寸
     */
    protected $imageWidths = [
        's' => 600,
        'm' => 1024,
        'l' => 1440,
    ];

    /**
     * @var float 图片高宽比
     */
    protected $imageScale = 0.56;

    /**
     * 获取默认的用户封面图片地址
     *
     * @return array
     */
    protected function getDefaultImageUrls(): array
    {
        $suffix = $this->isSupportWebp() ? 'webp' : 'jpg';
        $staticUrl = $this->getStaticUrl();
        $data = [];

        foreach (array_keys($this->imageWidths) as $size) {
            $data[$size] = "{$staticUrl}user-cover/default_{$size}.{$suffix}";
        }

        return $data;
    }

    /**
     * 上传指定用户的封面
     *
     * @param  int                   $userId
     * @param  UploadedFileInterface $cover
     * @return string
     */
    public function upload(int $userId, UploadedFileInterface $cover = null): string
    {
        $imageError = '';

        if (is_null($cover)) {
            $imageError = '请选择要上传的封面图片';
        }

        if (!$imageError) {
            $imageError = $this->validateImage($cover);
        }

        if ($imageError) {
            throw new ApiException(ErrorConstant::USER_COVER_UPLOAD_FAILED, false, $imageError);
        }

        $userInfo = $this->userModel->field(['user_id', 'cover'])->get($userId);
        if ($userInfo['cover']) {
            $this->deleteImage($userId, $userInfo['cover']);
        }

        $filename = $this->uploadImage($userId, $cover);
        $this->userModel->where(['user_id' => $userId])->update(['cover' => $filename]);

        return $filename;
    }

    /**
     * 删除指定用户的封面
     *
     * @param int $userId
     */
    public function delete(int $userId): void
    {
        $userInfo = $this->userModel->field(['user_id', 'cover'])->get($userId);
        if (!$userInfo) {
            throw new ApiException(ErrorConstant::USER_NOT_FOUND);
        }

        if ($userInfo['cover']) {
            $this->deleteImage($userId, $userInfo['cover']);
            $this->userModel->where(['user_id' => $userId])->update(['cover' => '']);
        }
    }
}
