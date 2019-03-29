<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Helper\StringHelper;
use App\Exception\ApiException;
use App\Traits\Brandable;
use Md\MDAvatars;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\UploadedFile;

/**
 * 用户头像管理
 *
 * Class UserAvatar
 * @package App\Service
 */
class UserAvatar extends ServiceAbstracts
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
            's' => [32, 32],
            'm' => [64, 64],
            'l' => [96, 96],
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
            throw new ApiException(ErrorConstant::USER_AVATAR_UPLOAD_FAILED, false, $imageError);
        }

        $userInfo = $this->container->userModel->field(['user_id', 'avatar'])->get($userId);

        $this->deleteImage($userId, $userInfo['avatar']);
        $filename = $this->uploadImage($userId, $avatar);
        $this->container->userModel->where(['user_id' => $userId])->update(['avatar' => $filename]);

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
        $userInfo = $this->container->userModel->field(['user_id', 'username', 'avatar'])->get($userId);
        if (!$userInfo) {
            throw new ApiException(ErrorConstant::USER_NOT_FOUND);
        }

        // 删除旧头像
        if ($userInfo['avatar']) {
            $this->deleteImage($userId, $userInfo['avatar']);
        }

        // 生成新头像
        $Avatar = new MDAvatars(mb_substr($userInfo['username'], 0, 1));
        $avatarTmpFilename = StringHelper::guid() . '.png';
        $avatarTmpPath = sys_get_temp_dir() . '/' . $avatarTmpFilename;
        $Avatar->Save($avatarTmpPath);
        $Avatar->Free();
        $uploadedFile = new UploadedFile($avatarTmpPath, $avatarTmpFilename, 'image/png');

        // 上传新头像
        $filename = $this->uploadImage($userId, $uploadedFile);
        $this->container->userModel->where(['user_id' => $userId])->update(['avatar' => $filename]);

        return $filename;
    }
}
