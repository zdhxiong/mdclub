<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;

/**
 * 关注
 *
 * Class FollowService
 * @package App\Service
 */
class FollowService extends ServiceAbstracts
{
    /**
     * 获取在 relationship 中使用的 is_following
     *
     * @param  array  $targetIds
     * @param  string $targetType
     * @return array              关注的对象的ID组成的数组
     */
    public function getIsFollowingInRelationship(array $targetIds, string $targetType): array
    {
        $currentUserId = $this->roleService->userId();
        $followingIds = [];

        if (!$currentUserId) {
            return $followingIds;
        }

        $followingIds = $this->followModel
            ->where([
                'user_id'         => $currentUserId,
                'followable_id'   => $targetIds,
                'followable_type' => $targetType,
            ])
            ->pluck('followable_id');

        return $followingIds;
    }
}
