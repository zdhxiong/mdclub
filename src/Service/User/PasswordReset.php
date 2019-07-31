<?php

declare(strict_types=1);

namespace MDClub\Service\User;

use MDClub\Exception\ValidationException;
use MDClub\Helper\Ip;
use MDClub\Helper\Validator;

/**
 * 重置密码
 */
class PasswordReset extends Abstracts
{
    /**
     * 发送邮件前的参数验证
     *
     * @param string $email
     */
    protected function sendEmailValidation(string $email): void
    {
        $needCaptcha = $this->captcha->isNextTimeNeed(
            Ip::getIpSign(),
            'send_password_reset_email',
            3,
            3600*24
        );

        $errors = [];

        if (!$email) {
            $errors['email'] = '邮箱不能为空';
        } elseif (!Validator::isEmail($email)) {
            $errors['email'] = '邮箱格式错误';
        } else {
            $userInfo = $this->model
                ->where('email', $email)
                ->field('disable_time')
                ->get();

            if (!$userInfo) {
                $errors['email'] = '该邮箱尚未注册';
            } elseif ($userInfo['disable_time']) {
                $errors['email'] = '该账号已被禁用';
            }
        }

        if ($errors) {
            throw new ValidationException($errors, $needCaptcha);
        }
    }

    /**
     * 密码重置时，发送邮箱验证邮件
     *
     * @param string $email
     */
    public function sendEmail(string $email): void
    {
        $this->sendEmailValidation($email);

        $code = $this->email->generateCode($email);
        $subject = '你正在重置' . $this->option->site_name . '的密码';
        $body = $this->view->fetch('/email_templates/password_reset.php', [
            'code' => $code,
            'option' => $this->option->onlyAuthorized()->all(),
        ]);

        $this->email->send($email, $subject, $body);
    }

    /**
     * 重置密码前的参数验证
     *
     * @param string $email
     * @param string $code
     * @param string $password
     */
    protected function resetValidation(string $email, string $code, string $password): void
    {
        $errors = [];

        if (!$email) {
            $errors['email'] = '邮箱不能为空';
        } elseif (!Validator::isEmail($email)) {
            $errors['email'] = '邮箱格式错误';
        }

        if (!$code) {
            $errors['email_code'] = '邮箱验证码不能为空';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        if (!$this->email->checkCode($email, $code)) {
            $errors['email_code'] = '邮箱验证码错误';
            throw new ValidationException($errors);
        }

        if (!$password) {
            $errors['password'] = '密码不能为空';
            throw new ValidationException($errors);
        }
    }

    /**
     * 重置密码
     *
     * @param string $email
     * @param string $code
     * @param string $password
     */
    public function reset(string $email, string $code, string $password): void
    {
        $this->resetValidation($email, $code, $password);

        $user = $this->model
            ->where('email', $email)
            ->field('user_id')
            ->get();

        $this->model
            ->where('user_id', $user['user_id'])
            ->update('password', $password);

        // 密码更新后，所有设备都需要重新登录，因此所有 token 都要失效
        $this->tokenModel
            ->where('user_id', $user['user_id'])
            ->delete();
    }
}
