<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * 字段验证异常
 *
 * Class ValidationException
 * @package App\Exception
 */
class ValidationException extends \Exception
{
    /**
     * 下次验证是否需要图形验证码
     *
     * @var bool
     */
    protected $needCaptcha = false;

    /**
     * 错误消息
     *
     * @var array
     */
    protected $errors = [];

    /**
     * ValidationException constructor.
     *
     * @param array $errors
     * @param bool  $needCaptcha
     */
    public function __construct(array $errors, bool $needCaptcha = false)
    {
        $this->needCaptcha = $needCaptcha;
        $this->errors = $errors;
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
     * 获取错误信息字段
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
