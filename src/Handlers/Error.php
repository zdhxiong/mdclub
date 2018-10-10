<?php

declare(strict_types=1);

namespace App\Handlers;

use Psr\Container\ContainerInterface;
use Slim\Http\Body;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;
use App\Exception\ValidationException;

/**
 * 异常处理
 *
 * 未处理的异常，在生产环境显示错误页面，调试环境交由 whoops 处理
 *
 * Class Exception
 *
 * @package App\Handlers
 */
class Error
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Exception constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param  Request    $request
     * @param  Response   $response
     * @param  \Exception $exception
     * @return Response
     */
    public function __invoke(Request $request, Response $response, \Exception $exception): Response
    {
        // 字段验证异常
        if ($exception instanceof ValidationException) {
            $output = [
                'code' => ErrorConstant::SYSTEM_FIELD_VERIFY_FAILED[0],
                'message' => ErrorConstant::SYSTEM_FIELD_VERIFY_FAILED[1],
                'errors' => $exception->getErrors(),
            ];

            $isNeedCaptcha = $exception->isNeedCaptcha();
        }

        // API 异常
        elseif ($exception instanceof ApiException) {
            $output = [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
            ];

            $extraMessage = $exception->getExtraMessage();
            if ($extraMessage) {
                $output['extra_message'] = $extraMessage;
            }

            $isNeedCaptcha = $exception->isNeedCaptcha();
        }

        // 未处理的异常
        else {
            // 调试环境交由 whoops 处理
            if (APP_DEBUG) {
                throw $exception;
            }

            $output = [
                'code' => ErrorConstant::SYSTEM_ERROR[0],
                'message' => ErrorConstant::SYSTEM_ERROR[1],
            ];

            $isNeedCaptcha = false;
        }

        if ($isNeedCaptcha) {
            /** @var \App\Service\CaptchaService $captchaService */
            $captchaService = $this->container->get(\App\Service\CaptchaService::class);

            $captchaInfo = $captchaService->build();
            $output['captcha_token'] = $captchaInfo['token'];
            $output['captcha_image'] = $captchaInfo['image'];
        }

        $body = new Body(fopen('php://temp', 'r+'));
        $body->write(json_encode($output));

        $response = $response
            ->withStatus(200)
            ->withHeader('Content-type', 'application/json')
            ->withBody($body);

        // 因为异常中不会自动调用中间件，所以这里手动调用中间件
        if (APP_DEBUG) {
            $response = (new \App\Middleware\TraceMiddleware($this->container))($request, $response, function () use ($response) {
                return $response;
            });
        }

        return $response;
    }
}
