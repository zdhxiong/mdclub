<?php

declare(strict_types=1);

namespace MDClub\Service\Token;

use MDClub\Exception\ValidationException;
use MDClub\Helper\Guid;
use MDClub\Helper\Ip;
use MDClub\Helper\Request;
use MDClub\Helper\Validator;

/**
 * 创建 Token
 */
class Create extends Abstracts
{
    /**
     * 登录前，对参数进行验证
     *
     * @param  string $name
     * @param  string $password
     * @param  string $device
     * @return int
     */
    protected function validation(string $name, string $password, string $device): int
    {
        $isNeedCaptcha = $this->captcha->isNextTimeNeed(
            Ip::getIp(),
            'create_token',
            3,
            3600*24
        );

        $errors = [];

        if (!$name) {
            $errors['name'] = '账号不能为空';
        }

        if (!$password) {
            $errors['password'] = '密码不能为空';
        }

        if (!Validator::isMax($device, 600)) {
            $errors['device'] = '设备信息不能超过 600 个字符';
        }

        if ($errors) {
            throw new ValidationException($errors, $isNeedCaptcha);
        }

        $userInfo = $this->userModel
            ->where(Validator::isEmail($name) ? 'email' : 'username', $name)
            ->field(['user_id', 'password', 'disable_time'])
            ->get();

        if (!$userInfo) {
            $errors['password'] = '账号或密码错误';
        } elseif ($userInfo['disable_time']) {
            $errors['name'] = '该账号已被禁用';
        } elseif (!password_verify($password, $userInfo['password'])) {
            $errors['password'] = '账号或密码错误';
        }

        if ($errors) {
            throw new ValidationException($errors, $isNeedCaptcha);
        }

        return $userInfo['user_id'];
    }

    /**
     * 登录
     *
     * @param  string $name
     * @param  string $password
     * @param  string $device
     * @return string           Token
     */
    public function create(string $name, string $password, string $device = ''): string
    {
        if (!$device) {
            $device = $this->request->getServerParams()['HTTP_USER_AGENT'] ?? '';
        }

        $userId = $this->validation($name, $password, $device);
        $token = Guid::generate();
        $requestTime = Request::time($this->request);

        $this->model->insert([
            'token' => $token,
            'user_id' => $userId,
            'device' => $device,
            'expire_time' => $requestTime + $this->auth->lifeTime,
        ]);

        $this->userModel
            ->where('user_id', $userId)
            ->update([
                'last_login_time' => $requestTime,
                'last_login_ip' => Ip::getIp(),
                'last_login_location' => Ip::getLocation(),
            ]);

        return $token;
    }
}
