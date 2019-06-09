<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ContainerAbstracts;

/**
 * 投票
 */
class Vote extends ContainerAbstracts
{
    /**
     * 获取在 relationship 中使用的 voting
     *
     * @param  array  $targetIds  投票目标ID
     * @param  string $targetType 投票目标类型（answer、comment、question、article）
     * @return array              键名为对象ID，键值为投票类型（up、down），未投票为空字符串
     */
    public function getInRelationship(array $targetIds, string $targetType): array
    {
        $currentUserId = $this->roleService->userId();
        $votings = array_combine($targetIds, array_fill(0, count($targetIds), ''));

        if (!$currentUserId) {
            return $votings;
        }

        return $this->voteModel
            ->field(['votable_id', 'type'])
            ->where([
                'user_id'      => $currentUserId,
                'votable_id'   => $targetIds,
                'votable_type' => $targetType,
            ])
            ->fetchCollection()
            ->select()
            ->keyBy('votable_id')
            ->map(static function ($item) {
                return $item['type'];
            })
            ->union($votings)
            ->all();
    }
}
