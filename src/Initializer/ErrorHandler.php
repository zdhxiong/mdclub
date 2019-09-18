<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use MDClub\Constant\ApiError;
use MDClub\Exception\ApiException;
use MDClub\Exception\ValidationException;
use MDClub\Middleware\Trace;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\ServerRequestCreatorFactory;
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
 * @property-read array                   $settings
 */
class ErrorHandler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var bool
     */
    protected $debug;

    /**
     * @param App       $app
     * @param Throwable $exception
     */
    public function __construct(App $app, Throwable $exception)
    {
        $this->container = $app->getContainer();
        $this->request = (ServerRequestCreatorFactory::create())->createServerRequestFromGlobals();
        $this->responseFactory = $app->getResponseFactory();
        $this->debug = $this->settings['debug'];

        if ($exception instanceof ValidationException) {
            $response = $this->handleValidationException($exception);
        } elseif ($exception instanceof ApiException) {
            $response = $this->handleApiException($exception);
        } elseif ($exception instanceof HttpNotFoundException) {
            $response = $this->handleNotFoundException();
        } elseif ($exception instanceof HttpMethodNotAllowedException) {
            $response = $this->handleNotAllowedException($exception);
        } else {
            $response = $this->handleUndefinedException($exception);
        }

        $response = $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PATCH, PUT, DELETE')
            ->withHeader(
                'Access-Control-Allow-Headers',
                'Token, Origin, X-Requested-With, Accept, Content-Type, Connection, User-Agent'
            );

        $responseEmitter = new ResponseEmitter();
        $responseEmitter->emit($response);
    }

    public function __get($name)
    {
        return $this->container->get($name);
    }

    /**
     * 处理验证异常，返回 json
     *
     * @param  ValidationException $exception
     * @return ResponseInterface
     */
    protected function handleValidationException(ValidationException $exception): ResponseInterface
    {
        $output = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'errors' => $exception->getErrors(),
        ];

        if ($exception->isNeedCaptcha()) {
            $output = $this->withCaptcha($output);
        }

        $response = $this->renderJson($output);

        if ($this->debug) {
            $response = (new Trace($this->container))->appendTraceMessage($this->request, $response);
        }

        return $response;
    }

    /**
     * 处理 API 异常，返回 json
     *
     * @param  ApiException      $exception
     * @return ResponseInterface
     */
    protected function handleApiException(ApiException $exception): ResponseInterface
    {
        $output = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
        ];

        $extraMessage = $exception->getExtraMessage();
        if ($extraMessage) {
            $output['extra_message'] = $extraMessage;
        }

        if ($exception->isNeedCaptcha()) {
            $output = $this->withCaptcha($output);
        }

        $response = $this->renderJson($output);

        if ($this->debug) {
            $response = (new Trace($this->container))->appendTraceMessage($this->request, $response);
        }

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
     * @param  HttpMethodNotAllowedException $exception
     * @return ResponseInterface
     */
    protected function handleNotAllowedException(HttpMethodNotAllowedException $exception): ResponseInterface
    {
        $allow = implode(', ', $exception->getAllowedMethods());

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
     * @param  Throwable         $exception
     * @return ResponseInterface
     */
    protected function handleUndefinedException(Throwable $exception): ResponseInterface
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

            $output = $whoops->prependHandler($handler)->handleException($exception);
            $response = $this->responseFactory->createResponse();

            return $response
                ->withHeader('Content-type', $contentType)
                ->withStatus(500)
                ->withBody((new StreamFactory())->createStream($output));
        }

        // 写入日志
        $this->log->error($exception->getMessage(), $exception->getTrace());

        if ($this->isRequestJson()) {
            return $this->renderJson([
                'code' => ApiError::SYSTEM_ERROR[0],
                'message' => ApiError::SYSTEM_ERROR[1],
            ]);
        }

        return $this->render500Html();
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
