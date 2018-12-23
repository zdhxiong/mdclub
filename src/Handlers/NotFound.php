<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Constant\ErrorConstant;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Handlers\AbstractHandler;
use Slim\Http\Body;

/**
 * 404 处理
 *
 * Class NotFound
 * @package App\Handlers
 */
class NotFound extends AbstractHandler
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * NotFound constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

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

            if ($contentType == 'application/json') {
                $status = 200;
                $output = $this->renderJsonNotFoundOutput();
            } else {
                $status = 404;
                $contentType = 'text/html';
                $output = $this->renderHtmlNotFoundOutput($request);
            }
        }

        $body = new Body(fopen('php://temp', 'r+'));
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
     * @param  ServerRequestInterface $request
     * @return string
     */
    protected function renderHtmlNotFoundOutput(ServerRequestInterface $request): string
    {
        /** @var \App\Library\ViewLibrary $view */
        $view = $this->container->get(\App\Library\ViewLibrary::class);

        return $view->fetch('/404.php');
    }
}
