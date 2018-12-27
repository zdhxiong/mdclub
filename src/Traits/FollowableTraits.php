<?php

declare(strict_types=1);

namespace App\Traits;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Helper\ArrayHelper;

/**
 * 可关注对象 （article、question、topic、user）
 *
 * Trait FollowableTraits
 * @package App\Traits
 */
trait FollowableTraits
{
    abstract public function hasOrFail(int $id);
    abstract public function addRelationship(array $data, array $relationship = []): array;
    abstract public function getPrivacyFields(): array;

    /**
     * 获取指定对象的关注者列表，带分页
     *
     * @param  int    $followableId
     * @param  bool   $withRelationship
     * @return array
     */
    public function getFollowers(int $followableId, bool $withRelationship = false): array
    {
        $this->hasOrFail($followableId);

        $list = $this->userModel
            ->join([
                '[><]follow' => ['user_id' => 'user_id'],
            ])
            ->where([
                'follow.followable_id'   => $followableId,
                'follow.followable_type' => $this->currentModel->table,
                'user.disable_time'      => 0,
            ])
            ->order([
                'follow.create_time' => 'DESC',
            ])
            ->field($this->userService->getPrivacyFields(), true)
            ->paginate();

        if ($withRelationship) {
            $relationship = [];

            if ($this->currentModel->table == 'user' && $followableId == $this->roleService->userId()) {
                $relationship = ['is_followed' => true];
            }

            $list['data'] = $this->userService->addRelationship($list['data'], $relationship);
        }

        return $list;
    }

    /**
     * 获取指定用户关注的对象列表，带分页
     *
     * @param  int   $userId
     * @param  bool  $withRelationship
     * @return array
     */
    public function getFollowing(int $userId, bool $withRelationship = false): array
    {
        $this->userService->hasOrFail($userId);

        $list = $this->currentModel
            ->join([
                '[><]follow' => [$this->currentModel->table . '_id' => 'followable_id'],
            ])
            ->where([
                'follow.user_id'         => $userId,
                'follow.followable_type' => $this->currentModel->table,
            ])
            ->order([
                'follow.create_time' => 'DESC'
            ])
            ->field($this->getPrivacyFields(), true)
            ->paginate();

        if ($withRelationship) {
            $relationship = $userId == $this->roleService->userId()
                ? ['is_following' => true]
                : [];

            $list['data'] = $this->addRelationship($list['data'], $relationship);
        }

        return $list;
    }

    /**
     * 添加关注
     *
     * @param  int    $userId
     * @param  int    $followableId
     */
    public function addFollow(int $userId, int $followableId): void
    {
        if ($this->currentModel->table == 'user' && $userId == $followableId) {
            throw new ApiException(ErrorConstant::USER_CANT_FOLLOW_YOURSELF);
        }

        if ($this->isFollowing($userId, $followableId)) {
            return;
        }

        $this->followModel->insert([
            'user_id'         => $userId,
            'followable_id'   => $followableId,
            'followable_type' => $this->currentModel->table,
        ]);

        $this->userModel
            ->where(['user_id' => $userId])
            ->update(["following_{$this->currentModel->table}_count[+]" => 1]);

        $this->currentModel
            ->where(["{$this->currentModel->table}_id" => $followableId])
            ->update(['follower_count[+]' => 1]);
    }

    /**
     * 取消关注
     *
     * @param  int    $userId
     * @param  int    $followableId
     */
    public function deleteFollow(int $userId, int $followableId): void
    {
        if (!$this->isFollowing($userId, $followableId)) {
            return;
        }

        $this->followModel->where([
            'user_id'         => $userId,
            'followable_id'   => $followableId,
            'followable_type' => $this->currentModel->table,
        ])->delete();

        $this->userModel
            ->where(['user_id' => $userId])
            ->update(["following_{$this->currentModel->table}_count[-]" => 1]);

        $this->currentModel
            ->where(["{$this->currentModel->table}_id" => $followableId])
            ->update(['follower_count[-]' => 1]);
    }

    /**
     * 获取指定对象的关注者数量
     *
     * @param  int $followableId
     * @return int
     */
    public function getFollowerCount(int $followableId): int
    {
        $followableTarget = $this->currentModel->field(['follower_count'])->get($followableId);

        return $followableTarget['follower_count'];
    }

    /**
     * 检查 userId 是否关注了指定对象
     *
     * @param  int    $userId
     * @param  int    $followableId
     * @return bool
     */
    protected function isFollowing(int $userId, int $followableId): bool
    {
        $this->userService->hasOrFail($userId);
        $this->hasOrFail($followableId);

        return $this->followModel->where([
            'user_id'         => $userId,
            'followable_id'   => $followableId,
            'followable_type' => $this->currentModel->table,
        ])->has();
    }
}
