<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\Abstracts;

/**
 * 验证码
 */
class Captcha extends Abstracts
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
