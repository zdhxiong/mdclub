<?php

declare(strict_types=1);

namespace App\Service;

use App\Constant\ErrorConstant;
use App\Interfaces\FollowableInterface;
use App\Exception\ApiException;
use App\Helper\ArrayHelper;

/**
 * 用户，已禁用用户也要当普通用户处理
 *
 * Class UserService
 * @package App\Service
 */
class UserService extends Service implements FollowableInterface
{
    /**
     * 获取隐私字段
     *
     * @return array
     */
    protected function getPrivacyFields(): array
    {
        return $this->roleService->managerId()
            ? []
            : [
                'email',
                'create_ip',
                'last_login_time',
                'last_login_ip',
                'notification_unread',
                'inbox_unread',
                'update_time',
            ];
    }

    /**
     * 获取允许排序的字段
     *
     * @return array
     */
    protected function getAllowOrderFields(): array
    {
        return $this->roleService->managerId()
            ? [
                'user_id',
                'last_login_time',
                'follower_count',
                'following_article_count',
                'following_question_count',
                'following_topic_count',
                'following_user_count',
                'article_count',
                'question_count',
                'answer_count',
                'notification_unread',
                'inbox_unread',
                'create_time',
                'update_time',
                'disable_time',
            ]
            : [];
    }

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    protected function getAllowFilterFields(): array
    {
        return $this->roleService->managerId()
            ? [
                'user_id',
                'username',
                'email',
                'create_ip',
                'last_login_ip',
                'disable_time',
            ]
            : [];
    }

    /**
     * 获取用户列表
     *
     * @param  bool $withRelationship
     * @return array
     */
    public function getList($withRelationship = false): array
    {
        $excludeFields = ArrayHelper::push(['password'], $this->getPrivacyFields());

        $list = $this->userModel
            ->where($this->getWhere(['disable_time' => 0]))        // 默认获取未禁用的用户
            ->order($this->getOrder(['follower_count' => 'DESC']))
            ->field($excludeFields, true)
            ->paginate();

        foreach ($list['data'] as &$item) {
            $item = $this->handle($item);
        }

        if ($withRelationship) {
            $list['data'] = $this->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 判断指定用户是否存在
     *
     * @param  int $userId
     * @return bool
     */
    public function has(int $userId): bool
    {
        // userId 为当前登录用户，不再进行判断
        if ($userId == $this->roleService->userId()) {
            return true;
        }

        return $this->userModel->has($userId);
    }

    /**
     * 若用户不存在，则抛出异常
     *
     * @param  int  $userId
     * @return bool
     */
    public function hasOrFail(int $userId): bool
    {
        if (!$isHas = $this->has($userId)) {
            throw new ApiException(ErrorConstant::USER_NOT_FOUND);
        }

        return $isHas;
    }

    /**
     * 根据用户ID的数组判断这些用户是否存在
     *
     * @param  array $userIds 用户ID数组
     * @return array          键名为用户ID，键值为bool值
     */
    public function hasMultiple(array $userIds): array
    {
        $userIds = array_unique($userIds);
        $result = [];

        if (!$userIds) {
            return $result;
        }

        // 已登录的用户不再判断
        $currentUserId = $this->roleService->userId();
        if ($currentUserId) {
            foreach ($userIds as $key => $userId) {
                if ($userId == $currentUserId) {
                    $result[$userId] = true;
                    unset($userIds[$key]);
                }
            }
        }

        if (!$userIds) {
            return $result;
        }

        $existUserIds = $this->userModel
            ->where(['user_id' => $userIds])
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $result[$userId] = in_array($userId, $existUserIds);
        }

        return $result;
    }

    /**
     * 获取用户信息
     *
     * @param  int   $userId
     * @param  bool  $withRelationship
     * @return array
     */
    public function get(int $userId, bool $withRelationship = false): array
    {
        $excludeFields = ArrayHelper::push(['password'], $this->getPrivacyFields());
        $userInfo = $this->userModel->field($excludeFields, true)->get($userId);

        if (!$userInfo) {
            throw new ApiException(ErrorConstant::USER_NOT_FOUND);
        }

        $userInfo = $this->handle($userInfo);

        if ($withRelationship) {
            $userInfo = $this->addRelationship($userInfo);
        }

        return $userInfo;
    }

    /**
     * 获取多个用户信息
     *
     * @param  array $userIds
     * @param  bool  $withRelationship
     * @return array
     */
    public function getMultiple(array $userIds, bool $withRelationship = false): array
    {
        if (!$userIds) {
            return [];
        }

        $excludeFields = ArrayHelper::push(['password'], $this->getPrivacyFields());
        $users = $this->userModel
            ->where(['user_id' => $userIds])
            ->field($excludeFields, true)
            ->select();

        foreach ($users as &$user) {
            $user = $this->handle($user);
        }

        if ($withRelationship) {
            $users = $this->userService->addRelationship($users);
        }

        return $users;
    }

    /**
     * 根据用户ID更新用户信息
     *
     * @param  int   $userId
     * @param  array $data
     * @return bool
     */
    public function update(int $userId, array $data): bool
    {
        $canUpdateFields = [
            'headline',
            'bio',
            'blog',
            'company',
            'location',
        ];

        $data = ArrayHelper::filter($data, $canUpdateFields);

        if (!$data) {
            return true;
        }

        return !!$this->userModel->where(['user_id' => $userId])->update($data);
    }

    /**
     * 禁用用户
     *
     * @param  int  $userId
     * @return bool
     */
    public function disable(int $userId): bool
    {
        $requestTime = $this->request->getServerParams()['REQUEST_TIME'];

        $rowCount = $this->userModel
            ->where(['user_id' => $userId])
            ->update(['disable_time' => $requestTime]);

        if (!$rowCount) {
            throw new ApiException(ErrorConstant::USER_NOT_FOUND);
        }

        // 禁用后，删除该用户的所有token
        $this->tokenModel->where(['user_id' => $userId])->delete();

        return true;
    }

    /**
     * 对数据库中读取的用户信息进行处理
     *
     * @param  array $userInfo
     * @return array
     */
    public function handle(array $userInfo): array
    {
        if (!$userInfo) {
            return $userInfo;
        }

        isset($userInfo['avatar']) && $userInfo['avatar'] = '';
        isset($userInfo['cover']) && $userInfo['cover'] = '';
        isset($userInfo['create_ip']) && $userInfo['create_ip'] = long2ip((int)$userInfo['create_ip']);
        isset($userInfo['last_login_ip']) && $userInfo['last_login_ip'] = long2ip((int)$userInfo['last_login_ip']);

        return $userInfo;
    }

    /**
     * 判断邮箱是否已注册
     *
     * @param  string $email
     * @return bool
     */
    public function isEmailExist(string $email): bool
    {
        return $this->userModel->where(['email' => $email])->has();
    }

    /**
     * 判断用户名是否已注册
     *
     * @param  string $username
     * @return bool
     */
    public function isUsernameExist(string $username): bool
    {
        return $this->userModel->where(['username' => $username])->has();
    }

    /**
     * 判断用户名或邮箱是否已注册
     *
     * @param  array       $data   ['username' => string, 'email' => string]
     * @return array|false         ['email' => true]
     */
    public function isEmailOrUsernameExist(array $data)
    {
        $users = $this->userModel
            ->field(['user_id', 'username', 'email'])
            ->where(['OR' => $data])
            ->select();

        if (!$users) {
            return false;
        }

        $result = [];
        foreach ($users as $user) {
            foreach ($data as $key => $value) {
                if ($user[$key] == $value) {
                    $result[$key] = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * 为用户信息添加 relationship 字段
     * {
     *     is_following: false, 登录用户是否已关注该用户
     *     is_followed: false,  该用户是否已关注登录用户
     *     is_me: false,        该用户是否是当前登录用户
     * }
     *
     * @param  array $users         用户信息，或多个用户组成的数组
     * @param  array $relationship  {is_followed: false, is_following: false} 若指定了该参数，则不再查询数据库
     * @return array
     */
    public function addRelationship(array $users, array $relationship = []): array
    {
        if (!$users) {
            return $users;
        }

        if (!$isArray = is_array(current($users))) {
            $users = [$users];
        }

        $currentUserId = $this->roleService->userId();
        $userIds = ArrayHelper::remove(array_unique(array_column($users, 'user_id')), $currentUserId);
        $followingUserIds = [];
        $followedUserIds = [];

        if ($userIds && $currentUserId && ($isArray || $currentUserId != $users[0]['user_id'])) {
            if (isset($relationship['is_followed'])) {
                $followedUserIds = $relationship['is_followed'] ? $userIds : [];
            } else {
                $followedUserIds = $this->followableModel->where([
                    'user_id' => $userIds,
                    'followable_id' => $currentUserId,
                    'followable_type' => 'user',
                ])->pluck('user_id');
            }

            if (isset($relationship['is_following'])) {
                $followingUserIds = $relationship['is_following'] ? $userIds : [];
            } else {
                $followingUserIds = $this->followableModel->where([
                    'user_id' => $currentUserId,
                    'followable_id' => $userIds,
                    'followable_type' => 'user',
                ])->pluck('followable_id');
            }
        }

        foreach ($users as &$user) {
            $user['relationship'] = [
                'is_followed' => in_array($user['user_id'], $followedUserIds),
                'is_following' => in_array($user['user_id'], $followingUserIds),
                'is_me' => $currentUserId == $user['user_id'],
            ];
        }

        if ($isArray) {
            return $users;
        }

        return $users[0];
    }
}
