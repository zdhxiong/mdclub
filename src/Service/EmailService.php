<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use Psr\Container\ContainerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use App\Helper\ValidatorHelper;
use App\Helper\StringHelper;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;

/**
 * Class EmailService
 *
 * @package App\Service
 */
class EmailService extends ServiceAbstracts
{
    /**
     * 邮件验证码有效期：3小时
     *
     * @var int
     */
    protected $lifeTime = 3600*3;

    /**
     * 每个邮件验证码最多验证次数
     *
     * @var int
     */
    protected $maxTimes = 20;

    /**
     * PHPMailer 实例
     *
     * @var PHPMailer
     */
    protected $mailer;

    /**
     * EmailService constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $options = $this->optionService->getAll();

        $PHPMailer = new PHPMailer(true);
        $PHPMailer->setLanguage($options['language']);
        $PHPMailer->isSMTP();
        $PHPMailer->setFrom($options['smtp_username'], $options['site_name']);
        $PHPMailer->isHTML(true);
        $PHPMailer->Host = $options['smtp_host'];
        $PHPMailer->SMTPAuth = true;
        $PHPMailer->Username = $options['smtp_username'];
        $PHPMailer->Password = $options['smtp_password'];
        $PHPMailer->SMTPSecure = $options['smtp_secure'];
        $PHPMailer->Port = $options['smtp_port'];
        $PHPMailer->CharSet = 'utf-8';

        if ($options['smtp_reply_to']) {
            $PHPMailer->addReplyTo($options['smtp_reply_to'], $options['site_name']);
        }

        $this->mailer = $PHPMailer;
    }

    /**
     * 发送邮件
     *
     * @param  array|bool $to
     * @param  string     $subject
     * @param  string     $body
     * @return bool
     */
    public function send($to, string $subject, string $body): bool
    {
        if (!is_array($to)) {
            $to = [$to];
        }

        $this->sendValidator($to, $subject, $body);

        foreach ($to as $address) {
            $this->mailer->addAddress($address);
        }

        $this->mailer->Subject = $subject;
        $this->mailer->Body = $body;

        try {
            $this->mailer->send();
            $this->mailer->clearAddresses();
        } catch (\Exception $e) {
            throw new ApiException(ErrorConstant::SYSTEM_SEND_EMAIL_FAILED, false, $e->getMessage());
        }

        return true;
    }

    /**
     * 检查验证码是否正确
     *
     * @param  string $email 邮箱
     * @param  string $code  验证码
     * @return bool
     */
    public function checkCode(string $email, string $code): bool
    {
        $codeInfo = $this->getCodeInfo($email);

        if (!$codeInfo) {
            return false;
        }

        // 同一个验证码使用次数限制，超过需要重新验证邮箱
        if ($codeInfo['times'] >= $this->maxTimes) {
            throw new ApiException(ErrorConstant::SYSTEM_EMAIL_VERIFY_EXPIRED);
        }

        $cacheKey = $this->getCacheKey($email);
        $cacheValue = $codeInfo['code'] . '-' . ($codeInfo['times'] + 1);

        $this->cache->set($cacheKey, $cacheValue, $this->lifeTime);

        return $codeInfo['code'] == $code;
    }

    /**
     * 发送注册验证码邮件
     *
     * @param  string $email
     * @return bool
     */
    public function sendRegisterEmail(string $email): bool
    {
        $option = $this->optionService->getAll();

        $code = $this->generateCode($email);
        $subject = '你正在注册' . $option['site_name'] . '账号';
        $body = $this->view->fetch('/email/register.php', [
            'code' => $code,
            'option' => $option,
        ]);

        return $this->send($email, $subject, $body);
    }

    /**
     * 发送重置密码验证码邮件
     *
     * @param  string $email
     * @return bool
     */
    public function sendPasswordResetEmail(string $email): bool
    {
        $option = $this->optionService->getAll();

        $code = $this->generateCode($email);
        $subject = '你正在重置' . $option['site_name'] . '的密码';
        $body = $this->view->fetch('/email/password_reset.php', [
            'code' => $code,
            'option' => $option,
        ]);

        return $this->send($email, $subject, $body);
    }

    /**
     * 发送注册成功的欢迎邮件
     *
     * @param  array        $user  用户信息
     * @return bool
     */
    public function sendWelcomeEmail(array $user): bool
    {
        $option = $this->optionService->getAll();

        $email = $user['email'];
        $subject = '你已成功注册' . $option['site_name'] . '账号';
        $body = $this->view->fetch('/email/welcome.php', [
            'user' => $user,
            'option' => $option,
        ]);

        return $this->send($email, $subject, $body);
    }

    /**
     * 发送邮件前对参数进行验证
     *
     * @param  array   $to
     * @param  string  $subject
     * @param  string  $body
     * @throws ValidationException
     */
    private function sendValidator(array $to, string $subject, string $body): void
    {
        $errors = [];
        $to = array_unique($to);

        if (empty($to)) {
            $errors['email'] = '邮箱地址不能为空';
        } else {
            foreach ($to as $address) {
                if (!ValidatorHelper::isEmail($address)) {
                    $errors['email'] = '邮箱格式错误：' . $address;
                    break;
                }
            }
        }

        if (empty($subject)) {
            $errors['subject'] = '邮件标题不能为空';
        }

        if (empty($body)) {
            $errors['body'] = '邮件正文不能为空';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }

    /**
     * 根据邮箱获取已发送的验证码和已验证次数
     *
     * @param  string      $email  邮箱
     * @return array|bool          false表示未发送过验证码
     */
    private function getCodeInfo(string $email)
    {
        $cacheKey = $this->getCacheKey($email);
        $codeInfo = $this->cache->get($cacheKey);

        if (!$codeInfo) {
            return false;
        }

        [$code, $times] = explode('-', $codeInfo);

        return [
            'code' => $code,
            'times' => $times,
        ];
    }

    /**
     * 生成要发送的验证码
     *
     * 若上一次发送的验证码未被验证过，则仍旧发送上次的验证码；若已被验证过，则生成新的验证码
     *
     * @param  string $email 邮箱
     * @return string
     */
    private function generateCode(string $email): string
    {
        $codeInfo = $this->getCodeInfo($email);

        if (!$codeInfo || $codeInfo['times']) {
            $code = StringHelper::rand(6);
            $cacheKey = $this->getCacheKey($email);

            $this->cache->set($cacheKey, "{$code}-0", $this->lifeTime);
        } else {
            $code = $codeInfo['code'];
        }

        return $code;
    }

    /**
     * 获取缓存键名
     *
     * @param  string $email
     * @return string
     */
    protected function getCacheKey(string $email): string
    {
        return 'email_code_' . md5($email);
    }
}
