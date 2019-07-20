<?php

declare(strict_types=1);

namespace MDClub\Transformer;

/**
 * 用户转换器
 *
 * @property-read \MDClub\Model\Follow       $followModel
 * @property-read \MDClub\Model\User         $userModel
 * @property-read \MDClub\Service\UserAvatar $userAvatarService
 * @property-read \MDClub\Service\UserCover  $userCoverService
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
            $item['avatar'] = $this->userAvatarService->getBrandUrls($item['user_id'], $item['avatar']);
        }

        if (isset($item['user_id'], $item['cover'])) {
            $item['cover'] = $this->userCoverService->getBrandUrls($item['user_id'], $item['cover']);
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
    protected function is_followed(array $items, array $knownRelationship): array
    {
        $currentUserId = $this->auth->userId();
        $userIds = collect($items)->pluck('user_id')->unique()->diff($currentUserId)->all();
        $followedUserIds = [];

        if ($userIds && $currentUserId) {
            if (isset($knownRelationship['is_followed'])) {
                $followedUserIds = $knownRelationship['is_followed'] ? $userIds : [];
            } else {
                $followedUserIds = $this->followModel->where([
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
    protected function is_following(array $items, array $knownRelationship): array
    {
        $userId = $this->auth->userId();
        $keys = collect($items)->pluck('user_id')->unique()->diff($userId)->all();
        $followingKeys = [];

        if ($keys && $userId) {
            if (isset($knownRelationship['is_following'])) {
                $followingKeys = $knownRelationship['is_following'] ? $keys : [];
            } else {
                $followingKeys = $this->followTransformer->getInRelationship($keys, 'user');
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
    protected function is_me(array $items): array
    {
        $userId = $this->auth->userId();

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

        $users = $this->userModel
            ->field(['user_id', 'avatar', 'username', 'headline'])
            ->select($userIds);

        return collect($users)
            ->keyBy('user_id')
            ->map(function ($item) {
                $item['avatar'] = $this->userAvatarService->getBrandUrls($item['user_id'], $item['avatar']);

                return $item;
            })
            ->unionFill($userIds)
            ->all();
    }
}
