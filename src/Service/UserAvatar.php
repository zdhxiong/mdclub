<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Helper\Guid;
use MDClub\Traits\Brandable;
use Md\MDAvatars;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;

/**
 * 用户头像管理
 */
class UserAvatar extends Abstracts
{
    use Brandable;

    /**
     * 图片类型
     *
     * @return string
     */
    protected function getBrandType(): string
    {
        return 'user-avatar';
    }

    /**
     * 图片尺寸，宽高比为 1
     *
     * @return array
     */
    protected function getBrandSize(): array
    {
        return [
            's' => [64, 64],
            'm' => [128, 128],
            'l' => [256, 256],
        ];
    }

    /**
     * 默认头像地址，每个用户都有独一无二的头像，因此这里无需返回默认头像
     *
     * @return array
     */
    protected function getDefaultBrandUrls(): array
    {
        return [];
    }

    /**
     * 上传指定用户的头像
     *
     * @param  int                   $userId
     * @param  UploadedFileInterface $avatar
     * @return string
     */
    public function upload(int $userId, UploadedFileInterface $avatar = null): string
    {
        $imageError = '';

        if (is_null($avatar)) {
            $imageError = '请选择要上传的头像图片';
        }

        if (!$imageError) {
            $imageError = $this->validateImage($avatar);
        }

        if ($imageError) {
            throw new ApiException(ApiError::USER_AVATAR_UPLOAD_FAILED, false, $imageError);
        }

        $userInfo = $this->userModel->field(['user_id', 'avatar'])->get($userId);
        $this->deleteImage($userId, $userInfo['avatar']);

        $filename = $this->uploadImage($userId, $avatar);
        $this->userModel->where('user_id', $userId)->update('avatar', $filename);

        return $filename;
    }

    /**
     * 删除指定用户的头像，并重置为默认头像
     *
     * @param  int    $userId
     * @return string         自动生成的新头像文件名
     */
    public function delete(int $userId): string
    {
        $userInfo = $this->userModel->field(['user_id', 'username', 'avatar'])->get($userId);
        if (!$userInfo) {
            throw new ApiException(ApiError::USER_NOT_FOUND);
        }

        // 删除旧头像
        if ($userInfo['avatar']) {
            $this->deleteImage($userId, $userInfo['avatar']);
        }

        // 生成新头像
        $Avatar = new MDAvatars(mb_substr($userInfo['username'], 0, 1));
        $avatarTmpFilename = Guid::generate() . '.png';
        $avatarTmpPath = sys_get_temp_dir() . '/' . $avatarTmpFilename;
        $Avatar->Save($avatarTmpPath);
        $Avatar->Free();
        $uploadedFile = new UploadedFile($avatarTmpPath, $avatarTmpFilename, 'image/png');

        // 上传新头像
        $filename = $this->uploadImage($userId, $uploadedFile);
        $this->userModel->where('user_id', $userId)->update('avatar', $filename);

        return $filename;
    }
}
