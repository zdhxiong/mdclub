<?php

declare(strict_types=1);

namespace MDClub\Validator;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Captcha;
use MDClub\Facade\Library\Email as EmailLibrary;
use MDClub\Facade\Model\UserModel;
use MDClub\Facade\Service\UserService;
use MDClub\Helper\Ip;

/**
 * 用户验证
 */
class User extends Abstracts
{
    protected $attributes = [
        'email' => '邮箱',
        'email_code' => '邮箱验证码',
        'username' => '用户名',
        'password' => '密码',
        'device' => '设备信息',
    ];

    /**
     * 邮箱在用户表中尚未注册
     *
     * @return $this
     */
    protected function emailNotExist(): self
    {
        if ($this->email()->skip()) {
            return $this;
        }

        $has = UserModel
            ::where('email', $this->value())
            ->has();

        if ($has) {
            $this->setError('该邮箱已被注册');
        }

        return $this;
    }

    /**
     * 邮箱在用户表中存在，且对应的用户未被禁用
     *
     * @return $this
     */
    protected function emailExistAndEnable(): self
    {
        if ($this->email()->skip()) {
            return $this;
        }

        $user = UserModel
            ::where('email', $this->value())
            ->field('disable_time')
            ->get();

        if (!$user) {
            $this->setError('该邮箱尚未注册');
        } elseif ($user['disable_time']) {
            $this->setError('该账号已被禁用');
        }

        return $this;
    }

    /**
     * 用户名格式
     *
     * @return $this
     */
    protected function username(): self
    {
        if ($this->string()->notEmpty()->length(2, 20)->skip()) {
            return $this;
        }

        if (!preg_match('/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u', $this->value())) {
            $this->setError('用户名不能包含特殊字符');
        } elseif ($this->isEmail($this->value())) {
            $this->setError('不能使用邮箱作为用户名');
        } elseif (preg_match('/^1\d{10}$/', $this->value())) {
            $this->setError('不能使用手机号作为用户名');
        }

        return $this;
    }

    /**
     * 用户名在用户表中尚未注册
     *
     * @return $this
     */
    protected function usernameNotExist(): self
    {
        if ($this->username()->skip()) {
            return $this;
        }

        $has = UserModel::where('username', $this->value())->has();

        if ($has) {
            $this->setError('该用户名已被注册');
        }

        return $this;
    }

    /**
     * 邮箱验证码
     *
     * @return $this
     */
    protected function emailCode(): self
    {
        if ($this->string()->notEmpty()->skip()) {
            return $this;
        }

        $isOk = EmailLibrary::checkCode($this->data['email'], $this->value());

        if (!$isOk) {
            $this->setError('邮箱验证码错误');
        }

        return $this;
    }

    /**
     * 发送注册邮件的验证
     *
     * @param array $data [email]
     *
     * @return array
     */
    public function sendRegisterEmail(array $data): array
    {
        $needCaptcha = Captcha::isNextTimeNeed(
            Ip::getIpSign(),
            'send_register_email',
            3,
            3600 * 24
        );

        return $this->data($data)
            ->field('email')->exist()->emailNotExist()
            ->validate($needCaptcha);
    }

    /**
     * 发送重置密码邮件的验证
     *
     * @param array $data [email]
     *
     * @return array
     */
    public function sendPasswordResetEmail(array $data): array
    {
        $needCaptcha = Captcha::isNextTimeNeed(
            Ip::getIpSign(),
            'send_password_reset_email',
            3,
            3600 * 24
        );

        return $this->data($data)
            ->field('email')->exist()->emailExistAndEnable()
            ->validate($needCaptcha);
    }

    /**
     * 注册时的验证
     *
     * 首先验证 email 和 email_code，若验证不通过，则不严重 username 和 password，用于前端设计注册时支持分步注册
     *
     * @param array $data [email, email_code, username, password]
     *
     * @return array
     */
    public function register(array $data): array
    {
        $data = $this->data($data)
            ->field('email')->exist()->emailNotExist()
            ->field('email_code')->exist()->string()->notEmpty()->emailCode()
            ->validate();

        return $this->data($data)
            ->field('username')->exist()->usernameNotExist()
            ->field('password')->exist()->string()->notEmpty()
            ->validate();
    }

    /**
     * 修改密码时验证
     *
     * 首先验证 email 和 email_code，若验证不通过，则不验证 password，用于前端设计重置密码时支持分步修改
     *
     * @param array $data [email, email_code, password]
     *
     * @return array
     */
    public function updatePassword(array $data): array
    {
        $data = $this->data($data)
            ->field('email')->exist()->string()->notEmpty()->email()
            ->field('email_code')->exist()->string()->notEmpty()->emailCode()
            ->validate();

        return $this->data($data)
            ->field('password')->exist()->string()->notEmpty()
            ->validate();
    }

    /**
     * 更新时的验证
     *
     * @param int   $userId
     * @param array $data [headline, bio, blog, company, location]
     *
     * @return array
     */
    public function update(int $userId, array $data): array
    {
        if ($userId !== Auth::userId()) {
            UserService::hasOrFail($userId);
        }

        $data = collect($data)->only(['headline', 'bio', 'blog', 'company', 'location'])->all();

        return $this->data($data)
            ->field('headline')->length(null, 40)
            ->field('bio')->length(null, 160)
            ->field('blog')->length(null, 255)
            ->field('company')->length(null, 255)
            ->field('location')->length(null, 255)
            ->validate();
    }
}
