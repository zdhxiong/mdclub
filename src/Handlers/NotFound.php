<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Constant\ErrorConstant;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Body;

/**
 * 404 处理
 */
class NotFound extends AbstractHandler
{
    /**
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($request->getMethod() === 'OPTIONS') {
            $status = 404;
            $contentType = 'text/plain';
            $output = $this->renderPlainNotFoundOutput();
        } else {
            $contentType = $this->determineContentType($request);

            if ($contentType === 'application/json') {
                $status = 200;
                $output = $this->renderJsonNotFoundOutput();
            } else {
                $status = 404;
                $contentType = 'text/html';
                $output = $this->renderHtmlNotFoundOutput();
            }
        }

        $body = new Body(fopen('php://temp', 'r+b'));
        $body->write($output);

        return $response
            ->withStatus($status)
            ->withHeader('Content-Type', $contentType)
            ->withBody($body);
    }

    /**
     * 纯文本消息
     *
     * @return string
     */
    protected function renderPlainNotFoundOutput(): string
    {
        return 'Not found';
    }

    /**
     * JSON 消息
     *
     * @return string
     */
    protected function renderJsonNotFoundOutput(): string
    {
        $json = [
            'code' => ErrorConstant::SYSTEM_API_NOT_FOUND[0],
            'message' => ErrorConstant::SYSTEM_API_NOT_FOUND[1],
        ];

        return json_encode($json);
    }

    /**
     * HTML 模板
     *
     * @return string
     */
    protected function renderHtmlNotFoundOutput(): string
    {
        return $this->view->fetch('/404.php');
    }
}
