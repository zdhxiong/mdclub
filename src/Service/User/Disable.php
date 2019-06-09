<?php

declare(strict_types=1);

namespace App\Service\User;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;

/**
 * 禁用用户
 */
class Disable extends Abstracts
{
    /**
     * 禁用用户
     *
     * NOTE: 禁用用户不会影响该用户发表的内容，也不会影响关注关系
     *
     * @param  int  $userId
     */
    public function disable(int $userId): void
    {
        $requestTime = $this->request->getServerParams()['REQUEST_TIME'];

        $rowCount = $this->userModel
            ->where('user_id', $userId)
            ->update('disable_time', $requestTime);

        if (!$rowCount) {
            throw new ApiException(ErrorConstant::USER_NOT_FOUND);
        }

        // 禁用后，删除该用户的所有token
        $this->tokenModel->where('user_id', $userId)->delete();
    }

    /**
     * 批量禁用用户
     *
     * @param  array $userIds
     */
    public function disableMultiple(array $userIds): void
    {
        if (!$userIds) {
            return;
        }

        $requestTime = $this->request->getServerParams()['REQUEST_TIME'];

        $rowCount = $this->userModel
            ->where('user_id', $userIds)
            ->update('disable_time', $requestTime);

        if ($rowCount) {
            $this->tokenModel->where('user_id', $userIds)->delete();
        }
    }

    /**
     * 启用用户
     *
     * @param int $userId
     */
    public function enable(int $userId): void
    {
        $rowCount = $this->userModel
            ->where('user_id', $userId)
            ->update('disable_time', 0);

        if (!$rowCount) {
            throw new ApiException(ErrorConstant::USER_NOT_FOUND);
        }
    }

    /**
     * 批量启用用户
     *
     * @param array $userIds
     */
    public function enableMultiple(array $userIds): void
    {
        if (!$userIds) {
            return;
        }

        $this->userModel
            ->where('user_id', $userIds)
            ->update('disable_time', 0);
    }
}
