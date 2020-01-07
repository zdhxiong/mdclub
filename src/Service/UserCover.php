<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Validator\UserCoverValidator;
use MDClub\Helper\Url;
use MDClub\Service\Traits\Brandable;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 用户封面服务
 */
class UserCover
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
        $suffix = Request::isSupportWebp() ? 'webp' : 'jpg';
        $staticUrl = Url::staticPath();
        $data['o'] = "{$staticUrl}default/user_cover.{$suffix}";

        foreach (array_keys($this->getBrandSize()) as $size) {
            $data[$size] = "{$staticUrl}default/user_cover_{$size}.{$suffix}";
        }

        return $data;
    }

    /**
     * 上传指定用户的封面
     *
     * @param int                     $userId
     * @param UploadedFileInterface[] $data [cover]
     *
     * @return string
     */
    public function upload(int $userId, array $data): string
    {
        $data = UserCoverValidator::upload($data);

        $user = UserModel::field(['user_id', 'cover'])->get($userId);
        if ($user['cover']) {
            $this->deleteImage($userId, $user['cover']);
        }

        $filename = $this->uploadImage($userId, $data['cover']);
        UserModel::where('user_id', $userId)->update('cover', $filename);

        return $filename;
    }

    /**
     * 删除指定用户的封面
     *
     * @param int $userId
     */
    public function delete(int $userId): void
    {
        $user = UserModel::field(['user_id', 'cover'])->get($userId);
        if (!$user) {
            throw new ApiException(ApiErrorConstant::USER_NOT_FOUND);
        }

        if ($user['cover']) {
            $this->deleteImage($userId, $user['cover']);
            UserModel::where('user_id', $userId)->update('cover', '');
        }
    }
}
