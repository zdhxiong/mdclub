<?php

declare(strict_types=1);

namespace MDClub\Transformer;

/**
 * 关注转换器
 *
 * @property-read \MDClub\Model\Follow $followModel
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
        $userId = $this->auth->userId();

        if (!$userId) {
            return [];
        }

        if (!$targetIds) {
            return [];
        }

        $followingIds = $this->followModel
            ->where([
                'user_id'         => $userId,
                'followable_id'   => $targetIds,
                'followable_type' => $targetType,
            ])
            ->pluck('followable_id');

        return $followingIds;
    }
}
