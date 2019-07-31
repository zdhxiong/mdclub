<?php

declare(strict_types=1);

namespace MDClub\Transformer;

/**
 * 投票转换器
 *
 * @property-read \MDClub\Model\Vote $voteModel
 */
class Vote extends Abstracts
{
    /**
     * 获取在 relationships 中使用的 voting
     *
     * @param  array  $targetIds  投票目标ID
     * @param  string $targetType 投票目标类型（answer, comment, question, article）
     * @return array              键名为对象ID，键值为投票类型（up, down），未投票为空字符串
     */
    public function getInRelationship(array $targetIds, string $targetType): array
    {
        $userId = $this->auth->userId();
        $emptyVotings = collect()->unionFill($targetIds, '')->all();

        if (!$userId) {
            return $emptyVotings;
        }

        $votings = $this->voteModel
            ->field(['votable_id', 'type'])
            ->where([
                'user_id'      => $userId,
                'votable_id'   => $targetIds,
                'votable_type' => $targetType,
            ])
            ->select();

        return collect($votings)
            ->keyBy('votable_id')
            ->map(function ($item) {
                return $item['type'];
            })
            ->union($emptyVotings)
            ->all();
    }
}
