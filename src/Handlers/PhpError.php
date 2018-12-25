<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Constant\ErrorConstant;
use App\Library\Logger;
use App\Library\View;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Handlers\AbstractHandler;
use Slim\Http\Body;

/**
 * PHP错误处理
 *
 * 调试环境直接重新抛出错误，交由 whoops 处理
 *
 * Class Error
 *
 * @package App\Handlers
 */
class PhpError extends AbstractHandler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * PhpError constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->logger = $container->get(Logger::class);
    }

    /**
     * @param  ServerRequestInterface  $request
     * @param  ResponseInterface       $response
     * @param  \Throwable              $error
     * @return ResponseInterface
     * @throws \Throwable
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Throwable $error)
    {
        // 调试环境直接抛出错误，交由 whoops 处理
        if (APP_DEBUG) {
            throw $error;
        }

        // 写入日志
        $this->logger->error($error->getMessage(), $error->getTrace());

        $contentType = $this->determineContentType($request);

        if ($contentType == 'application/json') {
            $status = 200;
            $output = $this->renderJsonErrorMessage();
        } else {
            $status = 500;
            $output = $this->renderHtmlErrorMessage();
        }

        $body = new Body(fopen('php://temp', 'r+'));
        $body->write($output);

        return $response
            ->withStatus($status)
            ->withHeader('Content-type', $contentType)
            ->withBody($body);
    }

    /**
     * 渲染 JSON 错误
     *
     * @return string
     */
    protected function renderJsonErrorMessage()
    {
        $json = [
            'code' => ErrorConstant::SYSTEM_ERROR[0],
            'message' => ErrorConstant::SYSTEM_ERROR[1],
        ];

        return json_encode($json);
    }

    /**
     * 渲染 HTML 错误页面
     *
     * @return mixed
     */
    protected function renderHtmlErrorMessage()
    {
        /** @var View $view */
        $view = $this->container->get(View::class);

        return $view->fetch('/500.php');
    }
}
