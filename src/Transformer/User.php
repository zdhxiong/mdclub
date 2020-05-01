<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Model\FollowModel;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Service\UserAvatarService;
use MDClub\Facade\Service\UserCoverService;
use MDClub\Facade\Transformer\FollowTransformer;

/**
 * 用户转换器
 */
class User extends Abstracts
{
    protected $table = 'user';
    protected $primaryKey = 'user_id';
    protected $availableIncludes = ['is_followed', 'is_following', 'is_me'];
    protected $userExcept = [
        'password',
        'email',
        'create_ip',
        'create_location',
        'last_login_time',
        'last_login_ip',
        'last_login_location',
        'notification_unread',
        'inbox_unread',
        'update_time',
        'disable_time',
    ];
    protected $managerExcept = ['password'];

    /**
     * 格式化用户信息
     *
     * @param  array $item
     * @return array
     */
    protected function format(array $item): array
    {
        if (isset($item['user_id'], $item['avatar'])) {
            $item['avatar'] = UserAvatarService::getBrandUrls($item['user_id'], $item['avatar']);
        }

        if (isset($item['user_id'], $item['cover'])) {
            $item['cover'] = UserCoverService::getBrandUrls($item['user_id'], $item['cover']);
        }

        if (isset($item['headline'])) {
            $item['headline'] = htmlentities($item['headline']);
        }

        if (isset($item['bio'])) {
            $item['bio'] = htmlentities($item['bio']);
        }

        if (isset($item['blog'])) {
            $item['blog'] = htmlentities($item['blog']);
        }

        if (isset($item['company'])) {
            $item['company'] = htmlentities($item['company']);
        }

        if (isset($item['location'])) {
            $item['location'] = htmlentities($item['location']);
        }

        return $item;
    }

    /**
     * 添加 is_followed 状态
     *
     * @param  array $items
     * @param  array $knownRelationship
     * @return array
     */
    protected function isFollowed(array $items, array $knownRelationship): array
    {
        $currentUserId = Auth::userId();
        $userIds = collect($items)->pluck('user_id')->unique()->diff($currentUserId)->all();
        $followedUserIds = [];

        if ($userIds && $currentUserId) {
            if (isset($knownRelationship['is_followed'])) {
                $followedUserIds = $knownRelationship['is_followed'] ? $userIds : [];
            } else {
                $followedUserIds = FollowModel::where([
                    'user_id' => $userIds,
                    'followable_id' => $currentUserId,
                    'followable_type' => 'user',
                ])->pluck('user_id');
            }
        }

        foreach ($items as &$item) {
            $item['relationships']['is_followed'] =  in_array($item['user_id'], $followedUserIds);
        }

        return $items;
    }

    /**
     * 添加 is_following 状态
     *
     * @param  array $items
     * @param  array $knownRelationship
     * @return array
     */
    protected function isFollowing(array $items, array $knownRelationship): array
    {
        $userId = Auth::userId();
        $keys = collect($items)->pluck('user_id')->unique()->diff($userId)->all();
        $followingKeys = [];

        if ($keys && $userId) {
            if (isset($knownRelationship['is_following'])) {
                $followingKeys = $knownRelationship['is_following'] ? $keys : [];
            } else {
                $followingKeys = FollowTransformer::getInRelationship($keys, 'user');
            }
        }

        foreach ($items as &$item) {
            $item['relationships']['is_following'] = in_array($item['user_id'], $followingKeys);
        }

        return $items;
    }

    /**
     * 添加 is_me 状态
     *
     * @param  array $items
     * @return array
     */
    protected function isMe(array $items): array
    {
        $userId = Auth::userId();

        foreach ($items as &$item) {
            $item['relationships']['is_me'] = $userId === $item['user_id'];
        }

        return $items;
    }

    /**
     * 获取 user 子资源
     *
     * @param  array $userIds
     * @return array
     */
    public function getInRelationship(array $userIds): array
    {
        if (!$userIds) {
            return [];
        }

        $users = UserModel
            ::field(['user_id', 'avatar', 'username', 'headline'])
            ->select($userIds);

        return collect($users)
            ->keyBy('user_id')
            ->map(function ($item) {
                $item['avatar'] = UserAvatarService::getBrandUrls($item['user_id'], $item['avatar']);
                $item['headline'] = htmlentities($item['headline']);

                return $item;
            })
            ->unionFill($userIds)
            ->all();
    }
}
