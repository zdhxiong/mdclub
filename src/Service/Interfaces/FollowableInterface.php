<?php

declare(strict_types=1);

namespace MDClub\Service\Interfaces;

/**
 * 可关注对象接口。用于 article, question, topic, user
 */
interface FollowableInterface
{
    /**
     * 添加关注
     *
     * @param  int    $followableId 关注目标ID
     */
    public function addFollow(int $followableId): void;

    /**
     * 取消关注
     *
     * @param  int  $followableId 关注目标ID
     */
    public function deleteFollow(int $followableId): void;

    /**
     * 获取指定对象的关注者列表，带分页
     *
     * @param  int   $followableId
     * @return array
     */
    public function getFollowers(int $followableId): array;

    /**
     * 获取指定用户关注的对象列表，带分页
     *
     * @param  int              $userId
     * @return array
     */
    public function getFollowing(int $userId): array;

    /**
     * 获取指定对象的关注者数量
     *
     * @param  int $followableId
     * @return int
     */
    public function getFollowerCount(int $followableId): int;
}
