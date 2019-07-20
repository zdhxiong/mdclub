<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Traits\Getable;
use Psr\Container\ContainerInterface;

/**
 * 用户
 *
 * @property-read \MDClub\Library\Captcha $captcha
 */
class User extends Abstracts
{
    use Getable {
        has as traitHas;
        hasMultiple as traitHasMultiple;
    }

    /**
     * @var \MDClub\Model\User
     */
    protected $model;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->model = $this->userModel;
    }

    /**
     * @inheritDoc
     */
    public function has(int $userId): bool
    {
        // userId 为当前登录用户，不再进行判断
        if ($userId === $this->auth->userId()) {
            return true;
        }

        return $this->traitHas($userId);
    }

    /**
     * @inheritDoc
     */
    public function hasMultiple(array $userIds): array
    {
        $userIds = array_unique($userIds);
        $result = [];

        if (!$userIds) {
            return $result;
        }

        // 已登录的用户不再判断
        $currentUserId = $this->auth->userId();
        if ($currentUserId) {
            foreach ($userIds as $key => $userId) {
                if ($userId === $currentUserId) {
                    $result[$userId] = true;
                    unset($userIds[$key]);
                }
            }
        }

        return array_merge($result, $this->traitHasMultiple($userIds));
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
     * 获取用户列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->userModel->getList();
    }

    /**
     * 获取已禁用的用户列表
     *
     * @return array
     */
    public function getDisabled(): array
    {
        return $this->userModel->getDisabled();
    }
}
