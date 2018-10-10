<?php

declare(strict_types=1);

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 文章
 *
 * Class ArticleController
 * @package App\Controller
 */
class ArticleController extends Controller
{
    /**
     * 文章列表页面
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function pageIndex(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 文章详情页
     *
     * @param Request $request
     * @param Response $response
     * @param int $article_id
     *
     * @return Response
     */
    public function pageDetail(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }
}
