<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Helper\RequestHelper;
use App\Traits\Brandable;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 用户封面管理
 *
 * Class UserCover
 * @package App\Service
 */
class UserCover extends ServiceAbstracts
{
    use Brandable;

    /**
     * 图片类型
     *
     * @return string
     */
    protected function getBrandType(): string
    {
        return 'user-cover';
    }

    /**
     * 图片尺寸
     *
     * @return array
     */
    protected function getBrandWidths(): array
    {
        return [
            's' => 600,
            'm' => 1024,
            'l' => 1440,
        ];
    }

    /**
     * 图片高宽比
     *
     * @return float
     */
    protected function getBrandScale(): float
    {
        return 0.56;
    }

    /**
     * 获取默认的用户封面图片地址
     *
     * @return array
     */
    protected function getDefaultBrandUrls(): array
    {
        $suffix = RequestHelper::isSupportWebp($this->container->request) ? 'webp' : 'jpg';
        $staticUrl = $this->getStaticUrl();
        $data = [];

        foreach (array_keys($this->getBrandWidths()) as $size) {
            $data[$size] = "{$staticUrl}default/user_cover_{$size}.{$suffix}";
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

        $userInfo = $this->container->userModel->field(['user_id', 'cover'])->get($userId);
        if ($userInfo['cover']) {
            $this->deleteImage($userId, $userInfo['cover']);
        }

        $filename = $this->uploadImage($userId, $cover);
        $this->container->userModel->where(['user_id' => $userId])->update(['cover' => $filename]);

        return $filename;
    }

    /**
     * 删除指定用户的封面
     *
     * @param int $userId
     */
    public function delete(int $userId): void
    {
        $userInfo = $this->container->userModel->field(['user_id', 'cover'])->get($userId);
        if (!$userInfo) {
            throw new ApiException(ErrorConstant::USER_NOT_FOUND);
        }

        if ($userInfo['cover']) {
            $this->deleteImage($userId, $userInfo['cover']);
            $this->container->userModel->where(['user_id' => $userId])->update(['cover' => '']);
        }
    }
}
