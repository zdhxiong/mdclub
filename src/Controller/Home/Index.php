<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Facade\Library\View;
use Psr\Http\Message\ResponseInterface;

/**
 * 首页
 */
class Index
{
    /**
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return View::render('/index.php');
    }
}
