<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ValidationException;
use App\Helper\IpHelper;
use App\Helper\StringHelper;
use App\Helper\ValidatorHelper;

/**
 * 用户登录
 *
 * Class UserLoginService
 * @package App\Service
 */
class UserLoginService extends Service
{
    /**
     * 登录
     *
     * @param  string $name     用户名或邮箱
     * @param  string $password 密码
     * @param  string $device   额外的设备信息，为空时自动获取 UserAgent
     * @return array            含 token 的用户信息
     */
    public function doLogin(string $name, string $password, string $device = ''): array
    {
        // 频率限制，若同一 ip 24小时内调用超过 3 次，则需要输入验证码
        $needCaptcha = $this->captchaService->isNextTimeNeed(
            IpHelper::get(),
            'create_token',
            3,
            3600*24
        );

        if (!$device) {
            $device = $this->request->getServerParam('HTTP_USER_AGENT');
        }

        $userInfo = $this->doLoginValidator($name, $password, $device, $needCaptcha);
        $token = $userInfo['token'] = StringHelper::guid();
        $requestTime = $this->request->getServerParam('REQUEST_TIME');

        $this->tokenModel->insert([
            'user_id' => $userInfo['user_id'],
            'token' => $token,
            'expire_time' => $requestTime + $this->tokenService->lifeTime,
            'device' => $device,
        ]);

        return $userInfo;
    }

    /**
     * 登录前，对参数进行验证
     *
     * @param  string $name
     * @param  string $password
     * @param  string $device
     * @param  bool   $needCaptcha
     * @return array
     */
    private function doLoginValidator(string $name, string $password, string $device, bool $needCaptcha): array
    {
        $errors = [];

        if (!$name) {
            $errors['name'] = '账号不能为空';
        }

        if (!$password) {
            $errors['password'] = '密码不能为空';
        }

        if (!ValidatorHelper::isMax($device, 600)) {
            $errors['device'] = '设备信息不能超过 600 个字符';
        }

        if ($errors) {
            throw new ValidationException($errors, $needCaptcha);
        }

        if (ValidatorHelper::isEmail($name)) {
            $userInfo = $this->userModel->where(['email' => $name])->get();
        } else {
            $userInfo = $this->userModel->where(['username' => $name])->get();
        }

        if (!$userInfo) {
            $errors['password'] = '账号或密码错误';
        } elseif ($userInfo['disable_time']) {
            $errors['name'] = '该账号已被禁用';
        } elseif (!password_verify($password, $userInfo['password'])) {
            $errors['password'] = '账号或密码错误';
        }

        if ($errors) {
            throw new ValidationException($errors, $needCaptcha);
        }

        unset($userInfo['password']);

        return $userInfo;
    }
}
