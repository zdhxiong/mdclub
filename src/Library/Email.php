<?php

declare(strict_types=1);

namespace MDClub\Library;

use Exception;
use MDClub\Constant\ApiErrorConstant;
use MDClub\Constant\OptionConstant;
use MDClub\Exception\ApiException;
use MDClub\Facade\Library\Cache as CacheFacade;
use MDClub\Facade\Library\View as ViewFacade;
use MDClub\Facade\Library\Option as OptionFacade;
use MDClub\Facade\Validator\EmailValidator;
use MDClub\Helper\Str;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * 邮件
 */
class Email
{
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
    protected $lifeTime = 3600 * 3;

    /**
     * 每个邮件验证码最多验证次数
     *
     * @var int
     */
    protected $maxTimes = 20;

    /**
     * 获取缓存键名
     *
     * @param string $email
     *
     * @return string
     */
    protected function getCacheKey(string $email): string
    {
        return 'email_code_' . md5($email);
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
            $PHPMailer->setLanguage(OptionFacade::get(OptionConstant::LANGUAGE));
            $PHPMailer->isSMTP();
            $PHPMailer->setFrom(
                OptionFacade::get(OptionConstant::SMTP_USERNAME),
                OptionFacade::get(OptionConstant::SITE_NAME)
            );
            $PHPMailer->isHTML();
            $PHPMailer->Host = OptionFacade::get(OptionConstant::SMTP_HOST);
            $PHPMailer->SMTPAuth = true;
            $PHPMailer->Username = OptionFacade::get(OptionConstant::SMTP_USERNAME);
            $PHPMailer->Password = OptionFacade::get(OptionConstant::SMTP_PASSWORD);
            $PHPMailer->SMTPSecure = OptionFacade::get(OptionConstant::SMTP_SECURE);
            $PHPMailer->Port = OptionFacade::get(OptionConstant::SMTP_PORT);
            $PHPMailer->CharSet = 'utf-8';

            if (OptionFacade::get(OptionConstant::SMTP_REPLY_TO)) {
                $PHPMailer->addReplyTo(
                    OptionFacade::get(OptionConstant::SMTP_REPLY_TO),
                    OptionFacade::get(OptionConstant::SITE_NAME)
                );
            }

            $this->mailer = $PHPMailer;
        }

        return $this->mailer;
    }

    /**
     * 发送邮件
     *
     * @param array|string $to      接收者邮箱（单个邮箱字符串，或多个邮箱组成的数组)
     * @param string       $subject 邮件标题
     * @param string       $body    邮件正文
     */
    public function send($to, string $subject, string $body): void
    {
        if (!is_array($to)) {
            $to = [$to];
        }

        $data = EmailValidator::send(
            [
                'email' => $to,
                'subject' => $subject,
                'content' => $body,
            ]
        );

        $mailer = $this->getMailer();

        foreach ($data['email'] as $address) {
            $mailer->addAddress($address);
        }

        $mailer->Subject = $data['subject'];
        $mailer->Body = $data['content'];

        try {
            $mailer->send();
            $mailer->clearAddresses();
        } catch (Exception $e) {
            throw new ApiException(ApiErrorConstant::COMMON_SEND_EMAIL_FAILED, false, $e->getMessage());
        }
    }

    /**
     * 根据邮件模板发送邮件
     *
     * @param string       $template 邮件模板路径
     * @param array|string $to       接收者邮箱（单个邮箱字符串，或多个邮箱组成的数组）
     * @param string       $subject  邮件标题
     * @param array        $data     邮件正文
     */
    public function sendByTemplate(string $template, $to, string $subject, array $data): void
    {
        $this->send($to, $subject, ViewFacade::fetch($template, $data));
    }

    /**
     * 根据邮箱获取已发送的验证码和已验证次数
     *
     * @param string $email 邮箱
     *
     * @return array|null          null表示未发送过验证码
     */
    protected function getCodeInfo(string $email): ?array
    {
        $cacheKey = $this->getCacheKey($email);
        $codeInfo = CacheFacade::get($cacheKey);

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
     * @param string $email 邮箱
     * @param string $code  验证码
     *
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
            throw new ApiException(ApiErrorConstant::COMMON_EMAIL_VERIFY_EXPIRED);
        }

        $cacheKey = $this->getCacheKey($email);
        $cacheValue = $codeInfo['code'] . '-' . ($codeInfo['times'] + 1);

        CacheFacade::set($cacheKey, $cacheValue, $this->lifeTime);

        return $codeInfo['code'] === $code;
    }

    /**
     * 生成要发送的验证码
     *
     * 若上一次发送的验证码未被验证过，则仍旧发送上次的验证码；若已被验证过，则生成新的验证码
     *
     * @param string $email 邮箱
     *
     * @return string
     */
    public function generateCode(string $email): string
    {
        $codeInfo = $this->getCodeInfo($email);

        if (!$codeInfo || $codeInfo['times']) {
            $code = Str::random(6);
            $cacheKey = $this->getCacheKey($email);

            CacheFacade::set($cacheKey, "{$code}-0", $this->lifeTime);
        } else {
            $code = $codeInfo['code'];
        }

        return $code;
    }
}
