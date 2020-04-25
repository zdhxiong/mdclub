<?php

declare(strict_types=1);

namespace MDClub\Controller\Admin;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\View;
use Psr\Http\Message\ResponseInterface;

/**
 * 管理后台首页
 */
class Index
{
    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        if (Auth::isNotManager()) {
            return View::render('/404.php');
        }

        return View::render('/admin.php');
    }
}
