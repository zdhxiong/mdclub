<?php

declare(strict_types=1);

namespace MDClub\Service\User;

use MDClub\Exception\ValidationException;
use MDClub\Helper\Ip;
use MDClub\Helper\Validator;

/**
 * 用户注册
 */
class Register extends Abstracts
{
    /**
     * 判断邮箱是否已注册
     *
     * @param  string $email
     * @return bool
     */
    protected function isEmailExist(string $email): bool
    {
        return $this->model->where('email', $email)->has();
    }

    /**
     * 判断用户名是否已注册
     *
     * @param  string $username
     * @return bool
     */
    protected function isUsernameExist(string $username): bool
    {
        return $this->model->where('username', $username)->has();
    }

    /**
     * 发送注册邮件前的验证
     *
     * @param string $email
     */
    protected function sendEmailValidation(string $email): void
    {
        $needCaptcha = $this->captcha->isNextTimeNeed(
            Ip::getIp(),
            'send_register_email',
            3,
            3600*24
        );

        $errors = [];

        if (!$email) {
            $errors['email'] = '邮箱不能为空';
        } elseif (!Validator::isEmail($email)) {
            $errors['email'] = '邮箱格式错误';
        } elseif ($this->isEmailExist($email)) {
            $errors['email'] = '该邮箱已被注册';
        }

        if ($errors) {
            throw new ValidationException($errors, $needCaptcha);
        }
    }

    /**
     * 注册时，发送邮箱验证邮件
     *
     * @param string $email
     */
    public function sendEmail(string $email): void
    {
        $this->sendEmailValidation($email);

        $code = $this->email->generateCode($email);
        $subject = '你正在注册' . $this->option->site_name . '账号';
        $body = $this->view->fetch('/email_templates/register.php', [
            'code' => $code,
            'option' => $this->option->onlyAuthorized()->all(),
        ]);

        $this->email->send($email, $subject, $body);
    }

    /**
     * 注册前进行字段验证
     *
     * @param  string $email
     * @param  string $emailCode
     * @param  string $username
     * @param  string $password
     * @param  string  $device
     */
    protected function registerValidation(
        string $email,
        string $emailCode,
        string $username,
        string $password,
        string $device
    ): void {
        $errors = [];

        if (!$email) {
            $errors['email'] = '邮箱不能为空';
        } elseif (!Validator::isEmail($email)) {
            $errors['email'] = '邮箱格式错误';
        }

        if (!$emailCode) {
            $errors['email_code'] = '邮箱验证码不能为空';
        }

        if (!$username) {
            $errors['username'] = '用户名不能为空';
        } elseif (!Validator::isChsAlphaNum($username)) {
            $errors['username'] = '用户名不能包含特殊字符';
        } elseif (!Validator::isLength($username, 2, 20)) {
            $errors['username'] = '用户名长度应在 2 - 20 个字符之间';
        } elseif (Validator::isEmail($username)) {
            $errors['username'] = '不要使用邮箱作为用户名';
        } elseif (Validator::isMobile($username)) {
            $errors['username'] = '不要使用手机号作为用户名';
        }

        if (!$password) {
            $errors['password'] = '密码不能为空';
        }

        if (!Validator::isMax($device, 600)) {
            $errors['device'] = '设备信息不能超过 600 个字符';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        if ($this->isEmailExist($email)) {
            $errors['email'] = '该邮箱已被注册';
            throw new ValidationException($errors);
        }

        if ($this->isUsernameExist($username)) {
            $errors['username'] = '该用户名已被注册';
            throw new ValidationException($errors);
        }

        if (!$this->email->checkCode($email, $emailCode)) {
            $errors['email_code'] = '邮箱验证码错误';
            throw new ValidationException($errors);
        }
    }

    /**
     * 发送注册成功欢迎邮件
     *
     * @param array $user 用户信息
     */
    protected function sendWelcomeEmail(array $user): void
    {
        $email = $user['email'];
        $subject = '你已成功注册' . $this->option->site_name . '账号';
        $body = $this->view->fetch('/email_templates/welcome.php', [
            'user' => $user,
            'option' => $this->option->onlyAuthorized()->all(),
        ]);

        $this->email->send($email, $subject, $body);
    }

    /**
     * 创建账号
     *
     * @param  string $email
     * @param  string $emailCode
     * @param  string $username
     * @param  string $password
     * @param  string $device
     * @return array             含 token 的用户信息
     */
    public function register(
        string $email,
        string $emailCode,
        string $username,
        string $password,
        string $device = ''
    ): array {
        if (!$device) {
            $device = $this->request->getServerParams()['HTTP_USER_AGENT'] ?? '';
        }

        $this->registerValidation($email, $emailCode, $username, $password, $device);

        // 创建用户
        $userId = (int) $this->model->insert([
            'username' => htmlentities($username),
            'email'    => $email,
            'password' => $password,
        ]);

        // 生成默认头像
        $this->userAvatarService->delete($userId);

        // 获取用户信息
        $userInfo = $this->userGetService->get($userId);
        unset($userInfo['password']);

        $this->sendWelcomeEmail($userInfo);

        return $userInfo;
    }
}
