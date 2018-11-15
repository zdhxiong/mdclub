<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;

/**
 * 用户角色
 *
 * @property-read \App\Service\RoleService  currentService
 *
 * Class RoleService
 * @package App\Service
 */
class RoleService extends ServiceAbstracts
{
    /**
     * 获取当前登录用户的 user_id，未登录则返回 false
     *
     * @return int|false
     */
    public function userId()
    {
        $tokenInfo = $this->tokenService->getTokenInfo();

        return $tokenInfo ? $tokenInfo['user_id'] : false;
    }

    /**
     * 获取当前登录用户的 user_id，未登录则抛出异常
     *
     * @return int
     */
    public function userIdOrFail(): int
    {
        $tokenInfo = $this->tokenService->getTokenInfoOrFail();

        return $tokenInfo['user_id'];
    }

    /**
     * 当前用户已登录，且具有管理员权限，则返回 user_id，否则返回 false
     *
     * @return int|false
     */
    public function managerId()
    {
        $user_id = $this->userId();

        return $user_id === 1 ? $user_id : false;
    }

    /**
     * 当前用户已登录，且具有管理员权限，则返回 user_id，否则抛出异常
     *
     * @return int
     */
    public function managerIdOrFail(): int
    {
        $user_id = $this->userIdOrFail();

        if ($user_id === 1) {
            return $user_id;
        }

        throw new ApiException(ErrorConstant::USER_NEED_MANAGE_PERMISSION);
    }
}
