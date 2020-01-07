<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Exception\ValidationException;
use MDClub\Facade\Library\Captcha;
use MDClub\Facade\Model\UserModel;
use MDClub\Helper\Ip;
use MDClub\Initializer\App;

/**
 * Token 验证
 */
class Token extends Abstracts
{
    protected $attributes = [
        'name' => '账号',
        'password' => '密码',
        'device' => '设备信息',
    ];

    /**
     * 创建时验证，验证成功返回 user_id
     *
     * @param array $data [name, password, device]
     *
     * @return int
     */
    public function create(array $data): int
    {
        $isNeedCaptcha = Captcha::isNextTimeNeed(
            Ip::getIpSign(),
            'create_token',
            App::$config['APP_DEBUG'] ? 30 : 3, // 调试模式放宽限制
            3600 * 24
        );

        $data = $this->data($data)
            ->field('name')->exist()->string()->notEmpty()
            ->field('password')->exist()->string()->notEmpty()
            ->field('device')->exist()->string()->length(null, 600)
            ->validate($isNeedCaptcha);

        $user = UserModel
            ::where($this->isEmail($data['name']) ? 'email' : 'username', $data['name'])
            ->field(['user_id', 'password', 'disable_time'])
            ->get();

        $errors = [];

        if (!$user) {
            $errors['password'] = '账号或密码错误';
        } elseif ($user['disable_time']) {
            $errors['name'] = '该账号已被禁用';
        } elseif (!password_verify($data['password'], $user['password'])) {
            $errors['password'] = '账号或密码错误';
        }

        if ($errors) {
            throw new ValidationException($errors, $isNeedCaptcha);
        }

        return $user['user_id'];
    }
}
