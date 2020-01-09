<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
use MDClub\Exception\NeedCaptchaExceptionInterface;
use MDClub\Exception\ValidationException;
use MDClub\Facade\Library\Captcha;
use MDClub\Facade\Library\Log;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Library\View;
use MDClub\Helper\Cors;
use MDClub\Middleware\Trace;
use Psr\Http\Message\ResponseInterface;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Factory\StreamFactory;
use Slim\ResponseEmitter;
use Throwable;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

/**
 * 错误处理
 */
class ErrorHandler
{
    /**
     * @param Throwable $exception
     */
    public function __construct(Throwable $exception)
    {
        switch (true) {
            case $exception instanceof ValidationException:
                $response = $this->handleValidationException($exception);
                break;

            case $exception instanceof ApiException:
                $response = $this->handleApiException($exception);
                break;

            case $exception instanceof HttpNotFoundException:
                $response = $this->handleNotFoundException();
                break;

            case $exception instanceof HttpMethodNotAllowedException:
                $response = $this->handleNotAllowedException($exception);
                break;

            default:
                $response = $this->handleUndefinedException($exception);
                break;
        }

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit(Cors::enable($response));
    }

    /**
     * 创建响应
     *
     * @param string $contentType
     * @param int    $status
     * @param string $body
     *
     * @return ResponseInterface
     */
    protected function createResponse(string $contentType, int $status, string $body): ResponseInterface
    {
        $response = App::$slim->getResponseFactory()->createResponse();

        return $response
            ->withHeader('Content-type', $contentType)
            ->withStatus($status)
            ->withBody((new StreamFactory())->createStream($body));
    }

    /**
     * 处理验证异常，返回 json
     *
     * @param ValidationException $exception
     *
     * @return ResponseInterface
     */
    protected function handleValidationException(ValidationException $exception): ResponseInterface
    {
        $output = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'errors' => $exception->getErrors(),
        ];

        $output = $this->withCaptcha($exception, $output);
        $response = $this->renderJson($output);
        $response = $this->withTrace($response);

        return $response;
    }

    /**
     * 处理 API 异常，返回 json
     *
     * @param ApiException $exception
     *
     * @return ResponseInterface
     */
    protected function handleApiException(ApiException $exception): ResponseInterface
    {
        $output = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        $output = $this->withExtraMessage($exception, $output);
        $output = $this->withCaptcha($exception, $output);
        $response = $this->renderJson($output);
        $response = $this->withTrace($response);

        return $response;
    }

    /**
     * 处理 NotFound 异常
     *
     * @return ResponseInterface
     */
    protected function handleNotFoundException(): ResponseInterface
    {
        if (Request::getMethod() === 'OPTIONS') {
            return $this->renderPlainText();
        }

        if (Request::isJson()) {
            return $this->renderJson([
                'code' => ApiErrorConstant::SYSTEM_API_NOT_FOUND[0],
                'message' => ApiErrorConstant::SYSTEM_API_NOT_FOUND[1],
            ]);
        }

        return $this->render404Html();
    }

    /**
     * 处理 NotAllowed 异常
     *
     * @param HttpMethodNotAllowedException $exception
     *
     * @return ResponseInterface
     */
    protected function handleNotAllowedException(HttpMethodNotAllowedException $exception): ResponseInterface
    {
        $allow = implode(', ', $exception->getAllowedMethods());
        $extraMessage = "Method not allowed. Must be one of: {$allow}";

        if (Request::getMethod() === 'OPTIONS') {
            return $this->renderPlainText($extraMessage);
        }

        if (Request::isJson()) {
            return $this->renderJson([
                'code' => ApiErrorConstant::SYSTEM_API_NOT_ALLOWED[0],
                'message' => ApiErrorConstant::SYSTEM_API_NOT_ALLOWED[1],
                'extra_message' => $extraMessage,
            ]);
        }

        return $this->render404Html();
    }

    /**
     * 处理其他异常
     *
     * @param Throwable $exception
     *
     * @return ResponseInterface
     */
    protected function handleUndefinedException(Throwable $exception): ResponseInterface
    {
        // 调试环境交由 whoops 处理
        if (App::$config['APP_DEBUG']) {
            $whoops = new Run();

            if (Request::isJson()) {
                $handler = new JsonResponseHandler();
                $handler->setJsonApi(true)->addTraceToOutput(true);
                $contentType = 'application/json';
            } else {
                $handler = new PrettyPageHandler();
                $contentType = 'text/html';
            }

            $output = $whoops->prependHandler($handler)->handleException($exception);

            return $this->createResponse($contentType, 500, $output);
        }

        $this->logError($exception);

        if (Request::isJson()) {
            return $this->renderJson([
                'code' => ApiErrorConstant::SYSTEM_ERROR[0],
                'message' => ApiErrorConstant::SYSTEM_ERROR[1],
            ]);
        }

        return $this->render500Html();
    }

    /**
     * 写入异常到日志
     *
     * @param Throwable $exception
     */
    protected function logError(Throwable $exception)
    {
        $message = $exception->getMessage();
        $trace = $exception->getTrace();

        Log::error($message, $trace);
    }

    /**
     * 在响应中包含 trace
     *
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    protected function withTrace(ResponseInterface $response): ResponseInterface
    {
        if (!App::$config['APP_DEBUG']) {
            return $response;
        }

        $trace = new Trace();

        return $trace->appendTraceMessage($response);
    }

    /**
     * 包含额外的错误描述
     *
     * @param ApiException $exception
     * @param array        $output
     *
     * @return array
     */
    protected function withExtraMessage(ApiException $exception, array $output): array
    {
        $extraMessage = $exception->getExtraMessage();

        if (!$extraMessage) {
            return $output;
        }

        $output['extra_message'] = $extraMessage;

        return $output;
    }

    /**
     * 包含验证码
     *
     * @param NeedCaptchaExceptionInterface $exception
     * @param array                         $output
     *
     * @return array
     */
    protected function withCaptcha(NeedCaptchaExceptionInterface $exception, array $output): array
    {
        if (!$exception->isNeedCaptcha()) {
            return $output;
        }

        $captchaInfo = Captcha::generate();

        $output['captcha_token'] = $captchaInfo['token'];
        $output['captcha_image'] = $captchaInfo['image'];

        return $output;
    }

    /**
     * 渲染成 json，返回 Response，状态码始终为 200
     *
     * @param  array             $output
     * @return ResponseInterface
     */
    protected function renderJson(array $output): ResponseInterface
    {
        $contentType = 'application/json;charset=utf-8';
        $body = (string) json_encode($output);

        return $this->createResponse($contentType, 200, $body);
    }

    /**
     * 渲染 404 html页面，状态码为404
     *
     * @return ResponseInterface
     */
    protected function render404Html(): ResponseInterface
    {
        $contentType = 'text/html';
        $body = View::fetch('/404.php');

        return $this->createResponse($contentType, 404, $body);
    }

    /**
     * 渲染 500 html页面，状态码为500
     *
     * @return ResponseInterface
     */
    protected function render500Html(): ResponseInterface
    {
        $contentType = 'text/html';
        $body = View::fetch('/500.php');

        return $this->createResponse($contentType, 500, $body);
    }

    /**
     * 渲染纯文本，状态码始终为 200
     *
     * @param  string            $output
     * @return ResponseInterface
     */
    protected function renderPlainText(string $output = ''): ResponseInterface
    {
        $contentType = 'text/plain';

        return $this->createResponse($contentType, 200, $output);
    }
}
