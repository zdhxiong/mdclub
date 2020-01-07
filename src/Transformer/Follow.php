<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Model\FollowModel;

/**
 * 关注转换器
 */
class Follow extends Abstracts
{
    /**
     * 获取在 relationships 中使用的 is_following
     *
     * @param  array  $targetIds
     * @param  string $targetType
     * @return array              关注的对象的ID组成的数组
     */
    public function getInRelationship(array $targetIds, string $targetType): array
    {
        if (!Auth::userId()) {
            return [];
        }

        if (!$targetIds) {
            return [];
        }

        return FollowModel::where([
                'user_id'         => Auth::userId(),
                'followable_id'   => $targetIds,
                'followable_type' => $targetType,
            ])
            ->pluck('followable_id');
    }
}
