<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;

/**
 * 投票
 *
 * Class Vote
 * @package App\Service
 */
class Vote extends ServiceAbstracts
{
    /**
     * 获取在 relationship 中使用的 voting
     *
     * @param  array  $targetIds
     * @param  string $targetType
     * @return array              键名为对象ID，键值为投票类型（up、down），未投票为空字符串
     */
    public function getVotingInRelationship(array $targetIds, string $targetType): array
    {
        $currentUserId = $this->container->roleService->userId();
        $votings = array_combine($targetIds, array_fill(0, count($targetIds), ''));

        if (!$currentUserId) {
            return $votings;
        }

        $votes = $this->container->voteModel
            ->where([
                'user_id'      => $currentUserId,
                'votable_id'   => $targetIds,
                'votable_type' => $targetType,
            ])
            ->field(['votable_id', 'type'])
            ->select();

        foreach ($votes as $vote) {
            $votings[$vote['votable_id']] = $vote['type'];
        }

        return $votings;
    }
}
