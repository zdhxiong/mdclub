<?php

declare(strict_types=1);

namespace MDClub\Traits;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;

/**
 * 可关注对象。用于 article, question, topic, user
 *
 * @property-read \MDClub\Library\Auth    $auth
 * @property-read \MDClub\Model\Abstracts $model
 * @property-read \MDClub\Model\Follow    $followModel
 * @property-read \MDClub\Model\User      $userModel
 * @property-read \MDClub\Service\User    $userService
 */
trait Followable
{
    /**
     * 添加关注
     *
     * @param  int    $followableId 关注目标ID
     */
    public function addFollow(int $followableId): void
    {
        $userId = $this->auth->userId();
        $table = $this->model->table;
        $isUser = $table === 'user';

        if ($isUser && $userId === $followableId) {
            throw new ApiException(ApiError::USER_CANT_FOLLOW_YOURSELF);
        }

        if ($this->isFollowing($followableId)) {
            return;
        }

        $this->followModel->insert([
            'user_id'         => $userId,
            'followable_id'   => $followableId,
            'followable_type' => $table,
        ]);

        $this->userModel
            ->where('user_id', $userId)
            ->inc($isUser ? 'followee_count' : "following_${table}_count")
            ->update();

        $this->model
            ->where("${table}_id", $followableId)
            ->inc('follower_count')
            ->update();
    }

    /**
     * 取消关注
     *
     * @param  int  $followableId 关注目标ID
     */
    public function deleteFollow(int $followableId): void
    {
        $userId = $this->auth->userId();
        $table = $this->model->table;
        $isUser = $table === 'user';

        if ($isUser && $userId === $followableId) {
            return;
        }

        if (!$this->isFollowing($followableId)) {
            return;
        }

        $this->followModel->where([
            'user_id'         => $userId,
            'followable_id'   => $followableId,
            'followable_type' => $table,
        ])->delete();

        $this->userModel
            ->where('user_id', $userId)
            ->dec($isUser ? 'followee_count' : "following_{$table}_count")
            ->update();

        $this->model
            ->where("{$table}_id", $followableId)
            ->dec('follower_count')
            ->update();
    }

    /**
     * 获取指定对象的关注者列表，带分页
     *
     * @param  int   $followableId
     * @return array
     */
    public function getFollowers(int $followableId): array
    {
        $table = $this->model->table;

        $this->{"${table}Service"}->hasOrFail($followableId);

        return $this->userModel
            ->join([
                '[><]follow' => ['user_id' => 'user_id'],
            ])
            ->where([
                'follow.followable_id'   => $followableId,
                'follow.followable_type' => $table,
                'user.disable_time'      => 0,
            ])
            ->order('follow.create_time', 'DESC')
            ->paginate();
    }

    /**
     * 获取指定用户关注的对象列表，带分页
     *
     * @param  int              $userId
     * @return array
     */
    public function getFollowing(int $userId): array
    {
        $table = $this->model->table;

        $this->userService->hasOrFail($userId);

        return $this->model
            ->join([
                '[><]follow' => ["${table}_id" => 'followable_id'],
            ])
            ->where([
                'follow.user_id'         => $userId,
                'follow.followable_type' => $table,
            ])
            ->order('follow.create_time', 'DESC')
            ->paginate();
    }

    /**
     * 获取指定对象的关注者数量
     *
     * @param  int $followableId
     * @return int
     */
    public function getFollowerCount(int $followableId): int
    {
        $followableTarget = $this->model->field('follower_count')->get($followableId);

        return $followableTarget['follower_count'] ?? 0;
    }

    /**
     * 检查 userId 是否关注了指定对象
     *
     * @param  int    $followableId
     * @return bool
     */
    protected function isFollowing(int $followableId): bool
    {
        $table = $this->model->table;
        $userId = $this->auth->userId();

        $this->{"${table}Service"}->hasOrFail($followableId);

        return $this->followModel->where([
            'user_id'         => $userId,
            'followable_id'   => $followableId,
            'followable_type' => $table,
        ])->has();
    }
}
