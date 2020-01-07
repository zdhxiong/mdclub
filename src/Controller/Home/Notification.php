<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Facade\Library\View;
use Psr\Http\Message\ResponseInterface;

/**
 * 通知页面
 */
class Notification
{
    /**
     * 通知列表页
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return View::render('/notification/index.php');
    }
}
