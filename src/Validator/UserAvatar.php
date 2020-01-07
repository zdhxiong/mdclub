<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Exception\ValidationException;

/**
 * 用户头像验证
 */
class UserAvatar extends Abstracts
{
    /**
     * 上传前验证
     *
     * @param array $data [avatar]
     *
     * @return array
     */
    public function upload(array $data): array
    {
        try {
            $data = $this->data($data)
                ->field('avatar')->exist()->uploadedImage(true)
                ->validate();
        } catch (ValidationException $e) {
            throw new ApiException(
                ApiErrorConstant::USER_AVATAR_UPLOAD_FAILED,
                false,
                $e->getErrors()['avatar']
            );
        }

        return $data;
    }
}
