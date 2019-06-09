<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Constant\ErrorConstant;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Body;
use Throwable;

/**
 * PHP错误处理
 *
 * 调试环境直接重新抛出错误，交由 whoops 处理
 */
class PhpError extends AbstractHandler
{
    /**
     * @param  ServerRequestInterface  $request
     * @param  ResponseInterface       $response
     * @param  Throwable              $error
     * @return ResponseInterface
     * @throws Throwable
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, Throwable $error)
    {
        // 调试环境直接抛出错误，交由 whoops 处理
        if (APP_DEBUG) {
            throw $error;
        }

        // 写入日志
        $this->logger->error($error->getMessage(), $error->getTrace());

        $contentType = $this->determineContentType($request);

        if ($contentType === 'application/json') {
            $status = 200;
            $output = $this->renderJsonErrorMessage();
        } else {
            $status = 500;
            $output = $this->renderHtmlErrorMessage();
        }

        $body = new Body(fopen('php://temp', 'r+b'));
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
    protected function renderJsonErrorMessage(): string
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
        return $this->view->fetch('/500.php');
    }
}
