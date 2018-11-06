<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\UploadedFile;
use Md\MDAvatars;
use App\Abstracts\BrandImageAbstracts;
use App\Constant\ErrorConstant;
use App\Helper\StringHelper;
use App\Exception\ApiException;

/**
 * 用户头像管理
 *
 * Class UserAvatarService
 * @package App\Service
 */
class UserAvatarService extends BrandImageAbstracts
{
    /**
     * @var string 图片类型
     */
    protected $imageType = 'user-avatar';

    /**
     * @var array 图片尺寸数组
     */
    protected $imageWidths = [
        's' => 32,
        'm' => 64,
        'l' => 96,
    ];

    /**
     * @var int 图片高度/宽度的比例
     */
    protected $imageScale = 1;

    /**
     * 默认头像地址，每个用户都有独一无二的头像，因此这里无需返回默认头像
     *
     * @return array
     */
    protected function getDefaultImageUrls(): array
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

        $userInfo = $this->userModel->field(['user_id', 'avatar'])->get($userId);

        $this->deleteImage($userId, $userInfo['avatar']);
        $filename = $this->uploadImage($userId, $avatar);
        $this->userModel->where(['user_id' => $userId])->update(['avatar' => $filename]);

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
        $this->userModel->where(['user_id' => $userId])->update(['avatar' => $filename]);

        return $filename;
    }
}
