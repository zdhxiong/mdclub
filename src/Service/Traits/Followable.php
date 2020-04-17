<?php

declare(strict_types=1);

namespace MDClub\Service\Traits;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Model\FollowModel;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Service\UserService;
use MDClub\Model\Abstracts as ModelAbstracts;

/**
 * 可关注对象。用于 question, article, topic, user
 */
trait Followable
{
    /**
     * @inheritDoc
     */
    abstract public function getModelInstance(): ModelAbstracts;

    /**
     * @inheritDoc
     */
    public function addFollow(int $followableId): void
    {
        $model = $this->getModelInstance();
        $table = $model->table;
        $isUser = $table === 'user';
        $userId = Auth::userId();

        if ($isUser && $userId === $followableId) {
            throw new ApiException(ApiErrorConstant::USER_CANT_FOLLOW_YOURSELF);
        }

        if ($this->isFollowing($followableId)) {
            return;
        }

        FollowModel
            ::set('user_id', $userId)
            ->set('followable_id', $followableId)
            ->set('followable_type', $table)
            ->insert();

        UserModel
            ::where('user_id', Auth::userId())
            ->inc($isUser ? 'followee_count' : "following_${table}_count")
            ->update();

        $model
            ->where("${table}_id", $followableId)
            ->inc('follower_count')
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function deleteFollow(int $followableId): void
    {
        $model = $this->getModelInstance();
        $table = $model->table;
        $isUser = $table === 'user';
        $userId = Auth::userId();

        if ($isUser && $userId === $followableId) {
            return;
        }

        if (!$this->isFollowing($followableId)) {
            return;
        }

        FollowModel
            ::where('user_id', $userId)
            ->where('followable_id', $followableId)
            ->where('followable_type', $table)
            ->delete();

        UserModel
            ::where('user_id', $userId)
            ->dec($isUser ? 'followee_count' : "following_{$table}_count")
            ->update();

        $model
            ->where("{$table}_id", $followableId)
            ->dec('follower_count')
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function getFollowers(int $followableId): array
    {
        $model = $this->getModelInstance();
        $table = $model->table;

        /** @var QuestionService $class */
        $class = '\MDClub\Facade\Service\\' . ucfirst($table) . 'Service';

        $class::hasOrFail($followableId);

        return UserModel
            ::join(
                [
                    '[><]follow' => ['user_id' => 'user_id'],
                ]
            )
            ->where(
                [
                    'follow.followable_id' => $followableId,
                    'follow.followable_type' => $table,
                    'user.disable_time' => 0,
                ]
            )
            ->order('follow.create_time', 'DESC')
            ->paginate();
    }

    /**
     * @inheritDoc
     */
    public function getFollowing(int $userId): array
    {
        $model = $this->getModelInstance();
        $table = $model->table;
        UserService::hasOrFail($userId);

        return $model
            ->join(
                [
                    '[><]follow' => ["${table}_id" => 'followable_id'],
                ]
            )
            ->where(
                [
                    'follow.user_id' => $userId,
                    'follow.followable_type' => $table,
                ]
            )
            ->order('follow.create_time', 'DESC')
            ->paginate();
    }

    /**
     * @inheritDoc
     */
    public function getFollowerCount(int $followableId): int
    {
        $model = $this->getModelInstance();
        $followableTarget = $model->field('follower_count')->get($followableId);

        return $followableTarget['follower_count'] ?? 0;
    }

    /**
     * 检查 userId 是否关注了指定对象
     *
     * @param int $followableId
     *
     * @return bool
     */
    protected function isFollowing(int $followableId): bool
    {
        $model = $this->getModelInstance();
        $table = $model->table;

        /** @var QuestionService $class */
        $class = '\MDClub\Facade\Service\\' . ucfirst($table) . 'Service';

        $class::hasOrFail($followableId);

        return FollowModel
            ::where('user_id', Auth::userId())
            ->where('followable_id', $followableId)
            ->where('followable_type', $table)
            ->has();
    }
}
