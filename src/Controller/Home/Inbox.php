<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Facade\Library\View;
use Psr\Http\Message\ResponseInterface;

/**
 * 私信页面
 */
class Inbox
{
    /**
     * 私信列表页
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return View::render('/inbox/index.php');
    }
}
