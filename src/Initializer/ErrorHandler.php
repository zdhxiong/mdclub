<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use MDClub\Constant\ApiErrorConstant;
use MDClub\Exception\ApiException;
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
     * @var Throwable
     */
    protected $exception;

    /**
     * @param Throwable $exception
     */
    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;

        switch (true) {
            case $exception instanceof ValidationException:
                $response = $this->handleValidationException();
                break;

            case $exception instanceof ApiException:
                $response = $this->handleApiException();
                break;

            case $exception instanceof HttpNotFoundException:
                $response = $this->handleNotFoundException();
                break;

            case $exception instanceof HttpMethodNotAllowedException:
                $response = $this->handleNotAllowedException();
                break;

            default:
                $response = $this->handleUndefinedException();
                break;
        }

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit(Cors::enable($response));
    }

    /**
     * 处理验证异常，返回 json
     *
     * @return ResponseInterface
     */
    protected function handleValidationException(): ResponseInterface
    {
        $output = [
            'code' => $this->exception->getCode(),
            'message' => $this->exception->getMessage(),
            'errors' => $this->exception->getErrors(),
        ];

        $output = $this->withCaptcha($output);
        $response = $this->renderJson($output);
        $response = $this->withTrace($response);

        return $response;
    }

    /**
     * 处理 API 异常，返回 json
     *
     * @return ResponseInterface
     */
    protected function handleApiException(): ResponseInterface
    {
        $output = [
            'code' => $this->exception->getCode(),
            'message' => $this->exception->getMessage(),
        ];

        $output = $this->withExtraMessage($output);
        $output = $this->withCaptcha($output);
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
     * @return ResponseInterface
     */
    protected function handleNotAllowedException(): ResponseInterface
    {
        $allow = implode(', ', $this->exception->getAllowedMethods());
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
     * @return ResponseInterface
     */
    protected function handleUndefinedException(): ResponseInterface
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

            $output = $whoops->prependHandler($handler)->handleException($this->exception);
            $response = App::$slim->getResponseFactory()->createResponse();

            return $response
                ->withHeader('Content-type', $contentType)
                ->withStatus(500)
                ->withBody((new StreamFactory())->createStream($output));
        }

        $this->logError();

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
     */
    protected function logError()
    {
        $message = $this->exception->getMessage();
        $trace = $this->exception->getTrace();

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
     * @param array $output
     * @return array
     */
    protected function withExtraMessage(array $output): array
    {
        $extraMessage = $this->exception->getExtraMessage();

        if (!$extraMessage) {
            return $output;
        }

        $output['extra_message'] = $extraMessage;

        return $output;
    }

    /**
     * 包含验证码
     *
     * @param  array $output
     * @return array
     */
    protected function withCaptcha(array $output): array
    {
        if (!$this->exception->isNeedCaptcha()) {
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
        $body = (string) json_encode($output);
        $response = App::$slim->getResponseFactory()->createResponse();

        return $response
            ->withHeader('Content-type', 'application/json;charset=utf-8')
            ->withStatus(200)
            ->withBody((new StreamFactory())->createStream($body));
    }

    /**
     * 渲染 404 html页面，状态码为404
     *
     * @return ResponseInterface
     */
    protected function render404Html(): ResponseInterface
    {
        $body = View::fetch('/404.php');
        $response = App::$slim->getResponseFactory()->createResponse();

        return $response
            ->withHeader('Content-type', 'text/html')
            ->withStatus(404)
            ->withBody((new StreamFactory())->createStream($body));
    }

    /**
     * 渲染 500 html页面，状态码为500
     *
     * @return ResponseInterface
     */
    protected function render500Html(): ResponseInterface
    {
        $body = View::fetch('/500.php');
        $response = App::$slim->getResponseFactory()->createResponse();

        return $response
            ->withHeader('Content-type', 'text/html')
            ->withStatus(500)
            ->withBody((new StreamFactory())->createStream($body));
    }

    /**
     * 渲染纯文本，状态码始终为 200
     *
     * @param  string            $output
     * @return ResponseInterface
     */
    protected function renderPlainText(string $output = ''): ResponseInterface
    {
        $response = App::$slim->getResponseFactory()->createResponse();

        return $response
            ->withHeader('Content-type', 'text/plain')
            ->withStatus(200)
            ->withBody((new StreamFactory())->createStream($output));
    }
}
