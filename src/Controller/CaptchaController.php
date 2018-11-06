<?php

declare(strict_types=1);

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 验证码
 *
 * Class CaptchaController
 * @package App\Controller
 */
class CaptchaController extends Controller
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

        return $this->success($response, [
            'captcha_token' => $captcha['token'],
            'captcha_image' => $captcha['image'],
        ]);
    }
}
