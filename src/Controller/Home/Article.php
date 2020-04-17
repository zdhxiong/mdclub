<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Facade\Library\View;
use Psr\Http\Message\ResponseInterface;

/**
 * 文章页面
 */
class Article
{
    /**
     * 文章列表页面
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return View::render('/article/index.php');
    }

    /**
     * 文章详情页面
     *
     * @return ResponseInterface
     */
    public function info(): ResponseInterface
    {
        return View::render('/article/info.php');
    }
}
