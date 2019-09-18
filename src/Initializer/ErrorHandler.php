<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Exception\ValidationException;
use MDClub\Helper\Response;
use MDClub\Middleware\Trace;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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
 *
 * @property-read \MDClub\Library\Log     $log
 * @property-read \MDClub\Library\View    $view
 * @property-read \MDClub\Library\Captcha $captcha
 * @property-read ServerRequestInterface  $request
 * @property-read array                   $settings
 */
class ErrorHandler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @var Throwable
     */
    protected $exception;

    /**
     * 魔术方法获取容器中的对象
     *
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->container->get($name);
    }

    /**
     * @param ContainerInterface       $container
     * @param ResponseFactoryInterface $responseFactory
     * @param Throwable                $exception
     */
    public function __construct(
        ContainerInterface $container,
        ResponseFactoryInterface $responseFactory,
        Throwable $exception
    ) {
        $this->container = $container;
        $this->responseFactory = $responseFactory;
        $this->exception = $exception;
        $this->debug = $this->settings['debug'];

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

        $response = Response::withCors($response);

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
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

        if ($this->exception->isNeedCaptcha()) {
            $output = $this->withCaptcha($output);
        }

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

        $extraMessage = $this->exception->getExtraMessage();
        if ($extraMessage) {
            $output['extra_message'] = $extraMessage;
        }

        if ($this->exception->isNeedCaptcha()) {
            $output = $this->withCaptcha($output);
        }

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
        if ($this->request->getMethod() === 'OPTIONS') {
            return $this->renderPlainText('Not found');
        }

        if ($this->isRequestJson()) {
            return $this->renderJson([
                'code' => ApiError::SYSTEM_API_NOT_FOUND[0],
                'message' => ApiError::SYSTEM_API_NOT_FOUND[1],
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

        if ($this->request->getMethod() === 'OPTIONS') {
            return $this->renderPlainText('Allowed methods: ' . $allow);
        }

        if ($this->isRequestJson()) {
            return $this->renderJson([
                'code' => ApiError::SYSTEM_API_NOT_ALLOWED[0],
                'message' => ApiError::SYSTEM_API_NOT_ALLOWED[1],
                'extra_message' => 'Method not allowed. Must be one of: ' . $allow,
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
        if ($this->debug) {
            $whoops = new Run();

            if ($this->isRequestJson()) {
                $handler = new JsonResponseHandler();
                $handler->setJsonApi(true)->addTraceToOutput(true);
                $contentType = 'application/json';
            } else {
                $handler = new PrettyPageHandler();
                $contentType = 'text/html';
            }

            $output = $whoops->prependHandler($handler)->handleException($this->exception);
            $response = $this->responseFactory->createResponse();

            return $response
                ->withHeader('Content-type', $contentType)
                ->withStatus(500)
                ->withBody((new StreamFactory())->createStream($output));
        }

        $this->logError();

        if ($this->isRequestJson()) {
            return $this->renderJson([
                'code' => ApiError::SYSTEM_ERROR[0],
                'message' => ApiError::SYSTEM_ERROR[1],
            ]);
        }

        return $this->render500Html();
    }

    /**
     * 写入异常到日志
     */
    protected function logError()
    {
        $this->log->error($this->exception->getMessage(), $this->exception->getTrace());
    }

    /**
     * 在响应中包含 trace
     *
     * @param  ResponseInterface $response
     * @return ResponseInterface
     */
    protected function withTrace(ResponseInterface $response): ResponseInterface
    {
        if (!$this->debug) {
            return $response;
        }

        $trace = new Trace($this->container);

        return $trace->appendTraceMessage($this->request, $response);
    }

    /**
     * 包含验证码
     *
     * @param  array $output
     * @return array
     */
    protected function withCaptcha(array $output): array
    {
        $captchaInfo = $this->captcha->generate();
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
        $response = $this->responseFactory->createResponse();

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
        $body = $this->view->fetch('/404.php');
        $response = $this->responseFactory->createResponse();

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
        $body = $this->view->fetch('/500.php');
        $response = $this->responseFactory->createResponse();

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
    protected function renderPlainText(string $output): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();

        return $response
            ->withHeader('Content-type', 'text/plain')
            ->withStatus(200)
            ->withBody((new StreamFactory())->createStream($output));
    }

    /**
     * 是否是 JSON 请求
     *
     * @return bool
     */
    protected function isRequestJson(): bool
    {
        $accept = $this->request->getHeaderLine('accept');

        return strpos($accept, 'application/json') > -1;
    }
}
