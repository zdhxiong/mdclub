<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Facade\Library\Captcha as CaptchaLibrary;

/**
 * 验证码 API
 */
class Captcha
{
    /**
     * 生成图形验证码
     *
     * @return array
     */
    public function create(): array
    {
        $captcha = CaptchaLibrary::generate(100, 36);

        return [
            'captcha_token' => $captcha['token'],
            'captcha_image' => $captcha['image'],
        ];
    }
}
