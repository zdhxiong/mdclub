<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Vendor\MDAvatars;
use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Validator\UserAvatarValidator;
use MDClub\Helper\Str;
use MDClub\Service\Traits\Brandable;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\UploadedFile;

/**
 * 用户头像服务
 */
class UserAvatar
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
            'small' => [64, 64],
            'middle' => [128, 128],
            'large' => [256, 256],
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
     * @param int                     $userId
     * @param UploadedFileInterface[] $data [avatar]
     *
     * @return string
     */
    public function upload(int $userId, array $data): string
    {
        $data = UserAvatarValidator::upload($data);

        $user = UserModel::field(['user_id', 'avatar'])->get($userId);
        $this->deleteImage($userId, $user['avatar']);

        $filename = $this->uploadImage($userId, $data['avatar']);
        UserModel::where('user_id', $userId)->update('avatar', $filename);

        return $filename;
    }

    /**
     * 删除指定用户的头像，并重置为默认头像
     *
     * @param int $userId
     *
     * @return string         自动生成的新头像文件名
     */
    public function delete(int $userId): string
    {
        $user = UserModel::field(['user_id', 'username', 'avatar'])->get($userId);
        if (!$user) {
            throw new ApiException(ApiErrorConstant::USER_NOT_FOUND);
        }

        // 删除旧头像
        if ($user['avatar']) {
            $this->deleteImage($userId, $user['avatar']);
        }

        // 生成新头像
        $Avatar = new MDAvatars(mb_substr($user['username'], 0, 1));
        $avatarTmpFilename = Str::guid() . '.png';
        $avatarTmpPath = sys_get_temp_dir() . '/' . $avatarTmpFilename;
        $Avatar->save($avatarTmpPath);
        $Avatar->free();
        $uploadedFile = new UploadedFile($avatarTmpPath, $avatarTmpFilename, 'image/png');

        // 上传新头像
        $filename = $this->uploadImage($userId, $uploadedFile);
        UserModel::where('user_id', $userId)->update('avatar', $filename);

        return $filename;
    }
}
