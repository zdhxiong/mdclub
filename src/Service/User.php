<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Constant\OptionConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Email;
use MDClub\Facade\Library\Option;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Model\TokenModel;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Service\UserAvatarService;
use MDClub\Facade\Service\UserService;
use MDClub\Facade\Validator\UserValidator;
use MDClub\Service\Interfaces\FollowableInterface;
use MDClub\Service\Interfaces\GetableInterface;
use MDClub\Service\Traits\Followable;
use MDClub\Service\Traits\Getable;

/**
 * 用户服务
 */
class User extends Abstracts implements FollowableInterface, GetableInterface
{
    use Followable;
    use Getable {
        has as traitHas;
        hasMultiple as traitHasMultiple;
    }

    /**
     * @inheritDoc
     */
    protected function getModel(): string
    {
        return \MDClub\Model\User::class;
    }

    /**
     * @inheritDoc
     */
    public function has($userId): bool
    {
        // userId 为当前登录用户，不再进行判断
        if ($userId === Auth::userId()) {
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
        $currentUserId = Auth::userId();
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
     * 启用指定用户
     *
     * @param int $userId
     */
    public function enable(int $userId): void
    {
        if (!$this->has($userId)) {
            throw new ApiException(ApiErrorConstant::USER_NOT_FOUND);
        }

        UserModel
            ::where('user_id', $userId)
            ->update('disable_time', 0);
    }

    /**
     * 批量启用用户
     *
     * @param array $userIds
     */
    public function enableMultiple(array $userIds): void
    {
        UserModel
            ::where('user_id', $userIds)
            ->update('disable_time', 0);
    }

    /**
     * 禁用指定用户
     *
     * NOTE: 禁用用户不会影响该用户发表的内容，也不会影响关注关系
     *
     * @param int $userId
     */
    public function disable(int $userId): void
    {
        if (!$this->has($userId)) {
            throw new ApiException(ApiErrorConstant::USER_NOT_FOUND);
        }

        UserModel
            ::where('user_id', $userId)
            ->update('disable_time', Request::time());

        // 禁用后，删除该用户的所有token
        TokenModel
            ::where('user_id', $userId)
            ->delete();
    }

    /**
     * 批量禁用用户
     *
     * @param array $userIds
     */
    public function disableMultiple(array $userIds): void
    {
        $disableCount = UserModel
            ::where('user_id', $userIds)
            ->update('disable_time', Request::time());

        if ($disableCount) {
            TokenModel
                ::where('user_id', $userIds)
                ->delete();
        }
    }

    /**
     * 注册时，发送邮箱验证邮件
     *
     * @param array $data [email]
     */
    public function sendRegisterEmail(array $data): void
    {
        $data = UserValidator::sendRegisterEmail($data);

        $template = '/email_templates/register.php';
        $to = $data['email'];
        $subject = '你正在注册' . Option::get(OptionConstant::SITE_NAME) . '账号';
        $templateData = [
            'code' => Email::generateCode($data['email']),
            'option' => Option::onlyAuthorized()->getAll(),
        ];

        Email::sendByTemplate($template, $to, $subject, $templateData);
    }

    /**
     * 密码重置时，发送邮箱验证邮件
     *
     * @param array $data [email]
     */
    public function sendPasswordResetEmail(array $data): void
    {
        $data = UserValidator::sendPasswordResetEmail($data);

        $template = '/email_templates/password_reset.php';
        $to = $data['email'];
        $subject = '你正在重置' . Option::get(OptionConstant::SITE_NAME) . '的密码';
        $templateData = [
            'code' => Email::generateCode($data['email']),
            'option' => Option::onlyAuthorized()->getAll(),
        ];

        Email::sendByTemplate($template, $to, $subject, $templateData);
    }

    /**
     * 创建账号
     *
     * @param array [email, email_code, username, password]
     *
     * @return array
     */
    public function register(array $data): array
    {
        $data = UserValidator::register($data);

        // 创建用户
        $userId = (int)UserModel
            ::set('username', $data['username'])
            ->set('email', $data['email'])
            ->set('password', $data['password'])
            ->insert();

        // 生成默认头像
        UserAvatarService::delete($userId);

        // 获取用户信息
        $user = UserService::get($userId);
        unset($user['password']);

        // 发送欢迎邮件
        $template = '/email_templates/welcome.php';
        $to = $user['email'];
        $subject = '你已成功注册' . Option::get(OptionConstant::SITE_NAME) . '账号';
        $templateData = [
            'user' => $user,
            'option' => Option::onlyAuthorized()->getAll(),
        ];
        Email::sendByTemplate($template, $to, $subject, $templateData);

        return $user;
    }

    /**
     * 重置密码
     *
     * @param array $data [email, email_code, password]
     */
    public function updatePassword(array $data): void
    {
        $data = UserValidator::updatePassword($data);

        $user = UserModel
            ::where('email', $data['email'])
            ->field('user_id')
            ->get();

        UserModel
            ::where('user_id', $user['user_id'])
            ->update('password', $data['password']);

        // 密码更新后，所有设备都需要重新登录，因此所有 token 都要失效
        TokenModel
            ::where('user_id', $user['user_id'])
            ->delete();
    }

    /**
     * 更新指定用户的信息
     *
     * @param int   $userId
     * @param array $data
     */
    public function update(int $userId, array $data): void
    {
        $data = UserValidator::update($userId, $data);

        if (!$data) {
            return;
        }

        UserModel
            ::where('user_id', $userId)
            ->update($data);
    }
}
