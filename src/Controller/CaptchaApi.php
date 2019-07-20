<?php

declare(strict_types=1);

namespace MDClub\Controller;

/**
 * 验证码
 *
 * @property-read \MDClub\Library\Captcha $captcha
 */
class CaptchaApi extends Abstracts
{
    /**
     * 生成图形验证码
     *
     * @return array
     */
    public function create(): array
    {
        $captcha = $this->captcha->generate(100, 36);

        return [
            'captcha_token' => $captcha['token'],
            'captcha_image' => $captcha['image'],
        ];
    }
}
