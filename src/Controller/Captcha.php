<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 验证码
 */
class Captcha extends ContainerAbstracts
{
    /**
     * 生成图形验证码
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $captcha = $this->captchaService->build();

        return collect([
            'captcha_token' => $captcha['token'],
            'captcha_image' => $captcha['image'],
        ])->render($response);
    }
}
