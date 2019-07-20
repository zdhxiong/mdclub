<?php

declare(strict_types=1);

namespace MDClub\Library;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Exception\ValidationException;
use MDClub\Helper\Random;
use MDClub\Helper\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * 邮件
 *
 * @property-read Option         $option
 * @property-read CacheInterface $cache
 */
class Email
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * PHPMailer 实例
     *
     * @var PHPMailer
     */
    protected $mailer;

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
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get(string $name)
    {
        return $this->container->get($name);
    }

    /**
     * 获取 PHPMailer 实例
     *
     * @return PHPMailer
     */
    protected function getMailer()
    {
        if (!$this->mailer) {
            $PHPMailer = new PHPMailer(true);
            $PHPMailer->setLanguage($this->option->language);
            $PHPMailer->isSMTP();
            $PHPMailer->setFrom($this->option->smtp_username, $this->option->site_name);
            $PHPMailer->isHTML();
            $PHPMailer->Host = $this->option->smtp_host;
            $PHPMailer->SMTPAuth = true;
            $PHPMailer->Username = $this->option->smtp_username;
            $PHPMailer->Password = $this->option->smtp_password;
            $PHPMailer->SMTPSecure = $this->option->smtp_secure;
            $PHPMailer->Port = $this->option->smtp_port;
            $PHPMailer->CharSet = 'utf-8';

            if ($this->option->smtp_reply_to) {
                $PHPMailer->addReplyTo($this->option->smtp_reply_to, $this->option->site_name);
            }

            $this->mailer = $PHPMailer;
        }

        return $this->mailer;
    }

    /**
     * 发送邮件前对参数进行验证
     *
     * @param  array               $to
     * @param  string              $subject
     * @param  string              $body
     * @throws ValidationException
     */
    protected function validation(array $to, string $subject, string $body): void
    {
        $errors = [];
        $to = array_unique($to);

        if (empty($to)) {
            $errors['email'] = '邮箱地址不能为空';
        } else {
            foreach ($to as $address) {
                if (!Validator::isEmail($address)) {
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
     * 发送邮件
     *
     * @param  array|string $to
     * @param  string       $subject
     * @param  string       $body
     */
    public function send($to, string $subject, string $body): void
    {
        if (!is_array($to)) {
            $to = [$to];
        }

        $this->validation($to, $subject, $body);

        $mailer = $this->getMailer();

        foreach ($to as $address) {
            $mailer->addAddress($address);
        }

        $mailer->Subject = $subject;
        $mailer->Body = $body;

        try {
            $mailer->send();
            $mailer->clearAddresses();
        } catch (\Exception $e) {
            throw new ApiException(ApiError::SYSTEM_SEND_EMAIL_FAILED, false, $e->getMessage());
        }
    }

    /**
     * 根据邮箱获取已发送的验证码和已验证次数
     *
     * @param  string      $email  邮箱
     * @return array|null          false表示未发送过验证码
     */
    protected function getCodeInfo(string $email): ?array
    {
        $cacheKey = $this->getCacheKey($email);
        $codeInfo = $this->cache->get($cacheKey);

        if (!$codeInfo) {
            return null;
        }

        [$code, $times] = explode('-', $codeInfo);

        return [
            'code' => $code,
            'times' => $times,
        ];
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
            throw new ApiException(ApiError::SYSTEM_EMAIL_VERIFY_EXPIRED);
        }

        $cacheKey = $this->getCacheKey($email);
        $cacheValue = $codeInfo['code'] . '-' . ($codeInfo['times'] + 1);

        $this->cache->set($cacheKey, $cacheValue, $this->lifeTime);

        return $codeInfo['code'] === $code;
    }

    /**
     * 生成要发送的验证码
     *
     * 若上一次发送的验证码未被验证过，则仍旧发送上次的验证码；若已被验证过，则生成新的验证码
     *
     * @param  string $email 邮箱
     * @return string
     */
    public function generateCode(string $email): string
    {
        $codeInfo = $this->getCodeInfo($email);

        if (!$codeInfo || $codeInfo['times']) {
            $code = Random::generate(6);
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
