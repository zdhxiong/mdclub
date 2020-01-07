<?php

declare(strict_types=1);

namespace MDClub\Middleware;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 非管理员访问时，显示 404 页面
 */
class ManagerOr404 implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (Auth::isNotManager()) {
            return View::render('/404.php');
        }

        return $handler->handle($request);
    }
}
