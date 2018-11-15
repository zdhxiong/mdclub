<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Exception\ValidationException;
use App\Helper\IpHelper;
use App\Helper\ValidatorHelper;

/**
 * 用户密码重置
 *
 * Class UserPasswordResetService
 * @package App\Service
 */
class UserPasswordResetService extends ServiceAbstracts
{
    /**
     * 密码重置时，发送邮箱验证邮件
     *
     * @param  string $email
     * @return bool
     */
    public function sendEmail(string $email): bool
    {
        $needCaptcha = $this->captchaService->isNextTimeNeed(
            IpHelper::get(),
            'send_password_reset_email',
            3,
            3600 * 24
        );

        $this->sendEmailValidator($email, $needCaptcha);

        return $this->emailService->sendPasswordResetEmail($email);
    }

    /**
     * 重置密码
     *
     * @param  string $email
     * @param  string $emailCode
     * @param  string $password
     * @return true
     */
    public function doReset(string $email, string $emailCode, string $password): bool
    {
        // 验证
        $this->doResetValidator($email, $emailCode, $password);

        $userInfo = $this->userModel
            ->field('user_id')
            ->where(['email' => $email])
            ->get();

        // 更新密码
        $this->userModel
            ->where(['user_id' => $userInfo['user_id']])
            ->update(['password' => $password]);

        // 密码更新后，所有设备都需要重新登录，因此所有 token 都要失效
        $this->tokenModel->where(['user_id' => $userInfo['user_id']])->delete();

        return true;
    }

    /**
     * 发送邮件前的参数验证
     *
     * @param  string $email
     * @param  bool $needCaptcha
     */
    private function sendEmailValidator(string $email, bool $needCaptcha): void
    {
        $errors = [];

        if (!$email) {
            $errors['email'] = '邮箱不能为空';
        } elseif (!ValidatorHelper::isEmail($email)) {
            $errors['email'] = '邮箱格式错误';
        }

        if (!$errors) {
            $userInfo = $this->userModel->where(['email' => $email])->get();

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
     * 重置密码前的参数验证
     *
     * @param  string $email
     * @param  string $emailCode
     * @param  string $password
     * @return void
     */
    private function doResetValidator(string $email, string $emailCode, string $password): void
    {
        $errors = [];

        if (!$email) {
            $errors['email'] = '邮箱不能为空';
        } elseif (!ValidatorHelper::isEmail($email)) {
            $errors['email'] = '邮箱格式错误';
        }

        if (!$emailCode) {
            $errors['email_code'] = '邮箱验证码不能为空';
        }

        if (!$errors && !$this->emailService->checkCode($email, $emailCode)) {
            $errors['email_code'] = '邮箱验证码错误';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        if (!$password) {
            $errors['password'] = '密码不能为空';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}
