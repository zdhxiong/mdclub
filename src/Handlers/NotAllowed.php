<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Constant\ErrorConstant;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Body;

/**
 * 405 处理
 *
 * NOTE: 输出 HTML 页面时，和 404 共用模板
 */
class NotAllowed extends AbstractHandler
{
    /**
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface      $response
     * @param  array                  $methods
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $methods)
    {
        if ($request->getMethod() === 'OPTIONS') {
            $status = 200;
            $contentType = 'text/plain';
            $output = $this->renderPlainOptionsMessage($methods);
        } else {
            $contentType = $this->determineContentType($request);

            if ($contentType === 'application/json') {
                $status = 200;
                $output = $this->renderJsonNotAllowedMessage($methods);
            } else {
                $status = 404;
                $contentType = 'text/html';
                $output = $this->renderHtmlNotAllowedMessage();
            }
        }

        $body = new Body(fopen('php://temp', 'r+b'));
        $body->write($output);

        return $response
            ->withStatus($status)
            ->withHeader('Content-type', $contentType)
            ->withBody($body);
    }

    /**
     * 纯文本消息
     *
     * @param  array   $methods
     * @return string
     */
    protected function renderPlainOptionsMessage(array $methods): string
    {
        $allow = implode(', ', $methods);

        return 'Allowed methods: ' . $allow;
    }

    /**
     * JSON 消息
     *
     * @param  array   $methods
     * @return string
     */
    protected function renderJsonNotAllowedMessage(array $methods): string
    {
        $allow = implode(', ', $methods);

        $json = [
            'code' => ErrorConstant::SYSTEM_API_NOT_ALLOWED[0],
            'message' => ErrorConstant::SYSTEM_API_NOT_ALLOWED[1],
            'extra_message' => 'Method not allowed. Must be one of: ' . $allow,
        ];

        return json_encode($json);
    }

    /**
     * HTML 模板
     *
     * @return string
     */
    protected function renderHtmlNotAllowedMessage(): string
    {
        return $this->view->fetch('/404.php');
    }
}
