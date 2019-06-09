<?php

declare(strict_types=1);

namespace App\Service\User;

/**
 * 更新用户
 */
class Update extends Abstracts
{
    /**
     * 根据用户ID更新用户信息
     *
     * @param  int   $userId
     * @param  array $data
     */
    public function update(int $userId, array $data): void
    {
        if ($userId !== $this->roleService->userId()) {
            $this->userService->hasOrFail($userId);
        }

        $data = collect($data)->only(['headline', 'bio', 'blog', 'company', 'location']);

        if ($data->isEmpty()) {
            return;
        }

        $this->userModel->where('user_id', $userId)->update($data);
    }
}
