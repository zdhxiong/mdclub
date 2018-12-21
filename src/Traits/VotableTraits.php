<?php

declare(strict_types=1);

namespace App\Traits;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Helper\ArrayHelper;

/**
 * 可投票对象 （question、answer、article、comment）
 *
 * Trait VotableTraits
 * @package App\Traits
 */
trait VotableTraits
{
    abstract public function hasOrFail(int $id);

    /**
     * 添加投票
     *
     * @param  int    $userId
     * @param  int    $votableId
     * @param  string $type
     */
    public function addVote(int $userId, int $votableId, string $type): void
    {
        if (!in_array($type, ['up', 'down'])) {
            throw new ApiException(ErrorConstant::SYSTEM_VOTE_TYPE_ERROR);
        }

        $this->userService->hasOrFail($userId);
        $this->hasOrFail($votableId);

        $voteWhere = [
            'user_id'      => $userId,
            'votable_id'   => $votableId,
            'votable_type' => $this->currentModel->table,
        ];

        $vote = $this->voteModel->where($voteWhere)->get();

        if (!$vote) {
            // 没有投过票时，添加投票
            $this->voteModel->insert(array_merge($voteWhere, ['type' => $type]));

            $voteCount = [$type == 'up' ? '+' : '-', 1];
        } elseif ($type != $vote['type']) {
            // 新的投票与旧的投票不同时，修改原始投票
            $this->voteModel->where($voteWhere)->update([
                'type' => $type,
                'create_time' => $this->request->getServerParam('REQUEST_TIME'),
            ]);

            $voteCount = [$type == 'up' ? '+' : '-' , 2];
        }

        // 更新投票数量
        if (isset($voteCount)) {
            $this->currentModel
                ->where([ $this->currentModel->table . '_id' => $votableId ])
                ->update([ 'vote_count[' . $voteCount[0] . ']' => $voteCount[1] ]);
        }
    }

    /**
     * 删除投票
     *
     * @param  int $userId
     * @param  int $votableId
     */
    public function deleteVote(int $userId, int $votableId): void
    {
        $this->userService->hasOrFail($userId);
        $this->hasOrFail($votableId);

        $voteWhere = [
            'user_id'      => $userId,
            'votable_id'   => $votableId,
            'votable_type' => $this->currentModel->table,
        ];

        $vote = $this->voteModel->where($voteWhere)->get();

        if ($vote) {
            $this->voteModel->where($voteWhere)->delete();

            $this->currentModel
                ->where([ $this->currentModel->table . '_id' => $votableId ])
                ->update([ 'vote_count[' . ($vote['type'] == 'up' ? '-' : '+') . ']' => 1 ]);
        }
    }

    /**
     * 获取投票总数（赞同票 - 反对票）
     *
     * @param int $votableId
     * @return int
     */
    public function getVoteCount(int $votableId): int
    {
        $votableTarget = $this->currentModel->field(['vote_count'])->get($votableId);

        return $votableTarget['vote_count'];
    }

    /**
     * 获取指定对象的投票者
     *
     * @param  int    $votableId
     * @param  string $type             投票类型：up、down，默认为获取所有类型
     * @param  bool   $withRelationship
     * @return array
     */
    public function getVoters(int $votableId, string $type = null, bool $withRelationship = false): array
    {
        $this->hasOrFail($votableId);

        // 需要查询的字段
        $fields = ArrayHelper::remove(
            $this->userModel->columns,
            $this->userService->getPrivacyFields()
        );

        foreach ($fields as &$field) {
            $field = 'user.' . $field;
        }

        // 查询条件
        $where = [
            'vote.votable_id'   => $votableId,
            'vote.votable_type' => $this->currentModel->table,
            'user.disable_time' => 0,
        ];

        if (in_array($type, ['up', 'down'])) {
            $where['vote.type'] = $type;
        }

        $list = $this->userModel
            ->join([
                '[><]vote' => ['user_id' => 'user_id'],
            ])
            ->where($where)
            ->order([
                'vote.create_time' => 'DESC',
            ])
            ->field($fields)
            ->paginate();

        if ($withRelationship) {
            $list['data'] = $this->userService->addRelationship($list['data']);
        }

        return $list;
    }
}
