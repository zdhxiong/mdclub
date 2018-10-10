<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * API 异常
 *
 * Class ApiException
 * @package App\Exception
 */
class ApiException extends \Exception
{
    /**
     * 下次验证是否需要图形验证码
     *
     * @var bool
     */
    protected $needCaptcha = false;

    /**
     * 额外的错误信息
     *
     * @var string
     */
    protected $extraMessage;

    /**
     * ApiException constructor.
     *
     * @param array  $error         错误代码
     * @param bool   $needCaptcha   下一次调用该接口是否需要输入图形验证码
     * @param string $extraMessage  额外的错误说明
     */
    public function __construct(array $error, bool $needCaptcha = false, string $extraMessage = '')
    {
        [$this->code, $this->message] = $error;
        $this->needCaptcha = $needCaptcha;
        $this->extraMessage = $extraMessage;
    }

    /**
     * 下次请求是否需要验证码
     *
     * @return bool
     */
    public function isNeedCaptcha(): bool
    {
        return $this->needCaptcha;
    }

    /**
     * 获取额外的错误说明
     *
     * @return string
     */
    public function getExtraMessage(): string
    {
        return $this->extraMessage;
    }
}
