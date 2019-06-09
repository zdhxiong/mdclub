<?php

declare(strict_types=1);

namespace App\Traits;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use Tightenco\Collect\Support\Collection;

/**
 * 可关注对象。用于 article, question, topic, user
 *
 * @property-read \App\Abstracts\ModelAbstracts     $model
 * @property-read \App\Service\User\Get             $userGetService
 * @property-read \App\Service\Role                 $roleService
 * @property-read \App\Model\Follow                 $followModel
 * @property-read \App\Model\User                   $userModel
 */
trait Followable
{
    protected $isForApi = false;

    /**
     * @return Followable
     */
    public function forApi(): self
    {
        $this->isForApi = true;

        return $this;
    }

    /**
     * 添加关注
     *
     * @param  int    $userId       关注者ID
     * @param  int    $followableId 关注目标ID
     */
    public function add(int $userId, int $followableId): void
    {
        $table = $this->model->table;
        $isUser = $table === 'user';

        if ($isUser && $userId === $followableId) {
            throw new ApiException(ErrorConstant::USER_CANT_FOLLOW_YOURSELF);
        }

        if ($this->isFollowing($userId, $followableId)) {
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
     * @param  int    $userId       关注者ID
     * @param  int    $followableId 关注目标ID
     */
    public function delete(int $userId, int $followableId): void
    {
        $table = $this->model->table;
        $isUser = $table === 'user';

        if (!$this->isFollowing($userId, $followableId)) {
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
     * @param  int              $followableId
     * @return array|Collection
     */
    public function getFollowers(int $followableId)
    {
        $table = $this->model->table;
        $isUser = $table === 'user';

        $this->{"${table}GetService"}->hasOrFail($followableId);

        $knownRelationship = [];

        if ($this->isForApi) {
            $this->userGetService->forApi();
            $this->isForApi = false;

            if ($isUser && $followableId === $this->roleService->userId()) {
                $knownRelationship = ['is_followed' => true];
            }
        }

        $this->userGetService->beforeGet();

        $result = $this->userModel
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

        $result = $this->userGetService->afterGet($result, $knownRelationship);

        return $this->userGetService->returnArray($result);
    }

    /**
     * 获取指定用户关注的对象列表，带分页
     *
     * @param  int              $userId
     * @return array|Collection
     */
    public function getFollowing(int $userId)
    {
        $this->userGetService->hasOrFail($userId);

        $table = $this->model->table;
        $knownRelationship = [];

        /** @var Getable $service */
        $service = $this->{"${table}GetService"};

        if ($this->isForApi) {
            $service->forApi();
            $this->isForApi = false;

            if ($userId === $this->roleService->userId()) {
                $knownRelationship = ['is_following' => true];
            }
        }

        $service->beforeGet();

        $result = $this->model
            ->join([
                '[><]follow' => ["${table}_id" => 'followable_id'],
            ])
            ->where([
                'follow.user_id'         => $userId,
                'follow.followable_type' => $table,
            ])
            ->order('follow.create_time', 'DESC')
            ->paginate();

        $result = $service->afterGet($result, $knownRelationship);

        return $service->returnArray($result);
    }

    /**
     * 获取指定对象的关注者数量
     *
     * @param  int $followableId
     * @return int
     */
    public function getFollowerCount(int $followableId): int
    {
        $followableTarget = $this->model->field(['follower_count'])->get($followableId);

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
        $this->userGetService->hasOrFail($userId);
        $this->{$this->model->table . 'GetService'}->hasOrFail($followableId);

        return $this->followModel->where([
            'user_id'         => $userId,
            'followable_id'   => $followableId,
            'followable_type' => $this->model->table,
        ])->has();
    }
}
