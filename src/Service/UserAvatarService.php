<?php

declare(strict_types=1);

namespace App\Service;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;

/**
 * 用户头像管理
 *
 * Class UserAvatarService
 * @package App\Service
 */
class UserAvatarService extends BrandImageService
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
     * 获取旧的图片 token
     *
     * @param  int    $userId
     * @return string
     */
    protected function getOldImageToken(int $userId): string
    {
        $userInfo = $this->userModel->field(['user_id', 'avatar'])->get($userId);

        if (!$userInfo) {
            throw new ApiException(ErrorConstant::USER_NOT_FOUND);
        }

        return $userInfo['avatar'];
    }




}
