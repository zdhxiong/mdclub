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

    /**
     * 获取指定用户关注的文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getFollowing(Request $request, Response $response, int $user_id): Response
    {
        return $response;
    }

    /**
     * 检查指定用户是否关注了指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @param  int      $article_id
     * @return Response
     */
    public function isFollowing(Request $request, Response $response, int $user_id, int $article_id): Response
    {
        return $response;
    }

    /**
     * 获取当前用户关注的文章列表
     *
     * @param  Request $request
     * @param  Response $response
     * @return Response
     */
    public function getMyFollowing(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 检查当前用户是否关注了指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function isMyFollowing(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }

    /**
     * 当前用户关注指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function addFollow(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }

    /**
     * 当前用户取消关注指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function deleteFollow(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }

    /**
     * 获取指定文章的关注者
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function getFollowers(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }
}
