<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Helper\Request;
use MDClub\Traits\Brandable;
use MDClub\Traits\Url;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 用户封面管理
 */
class UserCover extends Abstracts
{
    use Brandable, Url;

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
     * 图片尺寸，宽高比为 0.56
     *
     * @return array
     */
    protected function getBrandSize(): array
    {
        return [
            's' => [600, 336],
            'm' => [1050, 588],
            'l' => [1450, 812],
        ];
    }

    /**
     * 获取默认的用户封面图片地址
     *
     * @return array
     */
    protected function getDefaultBrandUrls(): array
    {
        $suffix = Request::isSupportWebp($this->request) ? 'webp' : 'jpg';
        $staticUrl = $this->getStaticUrl();
        $data['o'] = "{$staticUrl}default/user_cover.{$suffix}";

        foreach (array_keys($this->getBrandSize()) as $size) {
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
            throw new ApiException(ApiError::USER_COVER_UPLOAD_FAILED, false, $imageError);
        }

        $userInfo = $this->userModel->field(['user_id', 'cover'])->get($userId);
        if ($userInfo['cover']) {
            $this->deleteImage($userId, $userInfo['cover']);
        }

        $filename = $this->uploadImage($userId, $cover);
        $this->userModel->where('user_id', $userId)->update('cover', $filename);

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
            throw new ApiException(ApiError::USER_NOT_FOUND);
        }

        if ($userInfo['cover']) {
            $this->deleteImage($userId, $userInfo['cover']);
            $this->userModel->where('user_id', $userId)->update('cover', '');
        }
    }
}
