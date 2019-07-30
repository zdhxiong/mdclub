<?php

declare(strict_types=1);

namespace MDClub\Service\User;

use MDClub\Traits\Getable;

/**
 * 获取用户
 *
 * @property-read \MDClub\Model\User $model
 */
class Get extends Abstracts
{
    use Getable {
        has as traitHas;
        hasMultiple as traitHasMultiple;
    }

    /**
     * @inheritDoc
     */
    public function has(int $userId): bool
    {
        // userId 为当前登录用户，不再进行判断
        if ($userId === $this->auth->userId()) {
            return true;
        }

        return $this->traitHas($userId);
    }

    /**
     * @inheritDoc
     */
    public function hasMultiple(array $userIds): array
    {
        $userIds = array_unique($userIds);
        $result = [];

        if (!$userIds) {
            return $result;
        }

        // 已登录的用户不再判断
        $currentUserId = $this->auth->userId();
        if ($currentUserId) {
            foreach ($userIds as $key => $userId) {
                if ($userId === $currentUserId) {
                    $result[$userId] = true;
                    unset($userIds[$key]);
                }
            }
        }

        return array_merge($result, $this->traitHasMultiple($userIds));
    }

    /**
     * 获取用户列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }

    /**
     * 获取已禁用的用户列表
     *
     * @return array
     */
    public function getDisabled(): array
    {
        return $this->model->getDisabled();
    }
}
