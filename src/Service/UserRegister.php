<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Exception\ValidationException;
use App\Helper\IpHelper;
use App\Helper\StringHelper;
use App\Helper\ValidatorHelper;

/**
 * 用户注册
 *
 * Class UserRegister
 * @package App\Service
 */
class UserRegister extends ServiceAbstracts
{
    /**
     * 注册时，发送邮箱验证邮件
     *
     * @param  string $email
     */
    public function sendEmail(string $email): void
    {
        $needCaptcha = $this->container->captchaService->isNextTimeNeed(
            IpHelper::getIp(),
            'send_register_email',
            3,
            3600 * 24
        );

        $this->sendEmailValidator($email, $needCaptcha);
        $this->container->emailService->sendRegisterEmail($email);
    }

    /**
     * 创建账号
     *
     * @param  string $email
     * @param  string $emailCode
     * @param  string $username
     * @param  string $password
     * @param  string $device
     * @return array              含 token 的用户信息
     */
    public function doRegister(
        string $email,
        string $emailCode,
        string $username,
        string $password,
        string $device = ''
    ): array {
        if (!$device) {
            $device = $this->container->request->getServerParam('HTTP_USER_AGENT');
        }

        $this->doRegisterValidator($email, $emailCode, $username, $password, $device);

        // 创建用户
        $userId = (int)$this->container->userModel->insert([
            'username' => htmlentities($username),
            'email'    => $email,
            'password' => $password,
        ]);

        // 生成默认头像
        $this->container->userAvatarService->delete($userId);

        // 生成 token
        $token = StringHelper::guid();
        $requestTime = $this->container->request->getServerParam('REQUEST_TIME');
        $this->container->tokenModel->insert([
            'user_id'     => $userId,
            'token'       => $token,
            'expire_time' => $requestTime + $this->container->tokenService->lifeTime,
            'device'      => $device,
        ]);
        $this->container->tokenService->setToken($token);

        // 获取用户信息
        $userInfo = $this->container->userService->get($userId, true);
        $userInfo['token'] = $token;

        return $userInfo;
    }

    /**
     * 发送注册邮件前的验证
     *
     * @param  string $email
     * @param  bool   $needCaptcha
     */
    private function sendEmailValidator(string $email, bool $needCaptcha): void
    {
        $errors = [];

        if (!$email) {
            $errors['email'] = '邮箱不能为空';
        } elseif (!ValidatorHelper::isEmail($email)) {
            $errors['email'] = '邮箱格式错误';
        } elseif ($this->container->userService->isEmailExist($email)) {
            $errors['email'] = '该邮箱已被注册';
        }

        if ($errors) {
            throw new ValidationException($errors, $needCaptcha);
        }
    }

    /**
     * 注册前进行字段验证
     *
     * @param string $email
     * @param string $emailCode
     * @param string $username
     * @param string $password
     * @param string $device
     */
    private function doRegisterValidator(
        string $email,
        string $emailCode,
        string $username,
        string $password,
        string $device
    ): void {
        $errors = [];

        if (!$email) {
            $errors['email'] = '邮箱不能为空';
        } elseif (!ValidatorHelper::isEmail($email)) {
            $errors['email'] = '邮箱格式错误';
        }

        if (!$emailCode) {
            $errors['email_code'] = '邮箱验证码不能为空';
        }

        if (!$errors && $this->container->userService->isEmailExist($email)) {
            $errors['email'] = '该邮箱已被注册';
        }

        if (!$errors && !$this->container->emailService->checkCode($email, $emailCode)) {
            $errors['email_code'] = '邮箱验证码错误';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        if (!$username) {
            $errors['username'] = '用户名不能为空';
        } elseif (!ValidatorHelper::isChsAlphaNum($username)) {
            $errors['username'] = '用户名不能包含特殊字符';
        } elseif (!ValidatorHelper::isLength($username, 2, 20)) {
            $errors['username'] = '用户名长度应在 2 - 20 个字符之间';
        } elseif (ValidatorHelper::isEmail($username)) {
            $errors['username'] = '不要使用邮箱作为用户名';
        } elseif (ValidatorHelper::isMobile($username)) {
            $errors['username'] = '不要使用手机号作为用户名';
        }

        if (!$password) {
            $errors['password'] = '密码不能为空';
        }

        if (!ValidatorHelper::isMax($device, 600)) {
            $errors['device'] = '设备信息不能超过 600 个字符';
        }

        if (!$errors && $this->container->userService->isUsernameExist($username)) {
            $errors['username'] = '该用户名已被注册';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
