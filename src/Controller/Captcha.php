<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 验证码
 *
 * Class Captcha
 * @package App\Controller
 */
class Captcha extends ControllerAbstracts
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
        $captcha = $this->container->captchaService->build();

        return $this->success($response, [
            'captcha_token' => $captcha['token'],
            'captcha_image' => $captcha['image'],
        ]);
    }
}
