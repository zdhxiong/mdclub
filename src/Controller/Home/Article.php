<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Facade\Library\View;
use MDClub\Facade\Service\ArticleService;
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
     * todo 修改中间件，非 api 模块抛出异常时，显示 HTML 页面
     *
     * @param int $articleId
     *
     * @return ResponseInterface
     */
    public function info(int $articleId): ResponseInterface
    {
        return View::render(
            '/article/info.php',
            [
                'article' => ArticleService::getOrFail($articleId),
            ]
        );
    }
}
