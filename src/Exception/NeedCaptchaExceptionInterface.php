<?php

declare(strict_types=1);

namespace MDClub\Exception;

/**
 * 包含 isNeedCaptcha 方法的异常接口
 */
interface NeedCaptchaExceptionInterface
{
    /**
     * 下次请求是否需要验证码
     *
     * @return bool
     */
    public function isNeedCaptcha(): bool;
}
