<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Helper\Request;

/**
 * 用户启用与禁用
 */
class UserDisable extends Abstracts
{
    /**
     * 启用指定用户
     *
     * @param int $userId
     */
    public function enable(int $userId): void
    {
        $enableCount = $this->userModel
            ->where('user_id', $userId)
            ->update('disable_time', 0);

        if (!$enableCount) {
            throw new ApiException(ApiError::USER_NOT_FOUND);
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

    /**
     * 禁用指定用户
     *
     * NOTE: 禁用用户不会影响该用户发表的内容，也不会影响关注关系
     *
     * @param int $userId
     */
    public function disable(int $userId): void
    {
        $requestTime = Request::time($this->request);

        $disabledCount = $this->userModel
            ->where('user_id', $userId)
            ->update('disable_time', $requestTime);

        if (!$disabledCount) {
            throw new ApiException(ApiError::USER_NOT_FOUND);
        }

        // 禁用后，删除该用户的所有token
        $this->tokenModel->where('user_id', $userId)->delete();
    }

    /**
     * 批量禁用用户
     *
     * @param array $userIds
     */
    public function disableMultiple(array $userIds): void
    {
        if (!$userIds) {
            return;
        }

        $requestTime = Request::time($this->request);

        $disableCount = $this->userModel
            ->where('user_id', $userIds)
            ->update('disable_time', $requestTime);

        if ($disableCount) {
            $this->tokenModel->where('user_id', $userIds)->delete();
        }
    }
}
