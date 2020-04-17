<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Facade\Library\View;
use Psr\Http\Message\ResponseInterface;

/**
 * 提问页面
 */
class Question
{
    /**
     * 提问列表页
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return View::render('/question/index.php');
    }

    /**
     * 提问详情页
     *
     * @return ResponseInterface
     */
    public function info(): ResponseInterface
    {
        return View::render('/question/info.php');
    }

    /**
     * 回答详情页
     *
     * @return ResponseInterface
     */
    public function answer(): ResponseInterface
    {
        return View::render('/question/info.php');
    }
}
