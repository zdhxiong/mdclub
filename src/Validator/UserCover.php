<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Exception\ValidationException;

/**
 * 用户封面验证
 */
class UserCover extends Abstracts
{
    /**
     * 上传前验证
     *
     * @param array $data [cover]
     *
     * @return array
     */
    public function upload(array $data): array
    {
        try {
            $data = $this->data($data)
                ->field('cover')->exist()->uploadedImage(true)
                ->validate();
        } catch (ValidationException $e) {
            throw new ApiException(
                ApiErrorConstant::USER_COVER_UPLOAD_FAILED,
                false,
                $e->getErrors()['cover']
            );
        }

        return $data;
    }
}
