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
     * 获取文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 发表文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 获取指定文章详情
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function getOne(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }

    /**
     * 更新指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function update(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }

    /**
     * 删除指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function delete(Request $request, Response $response, int $article_id): Response
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

    /**
     * 获取指定文章下的评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function getComments(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }

    /**
     * 在指定文章下发表评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function createComment(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }

    /**
     * 更新指定评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function updateComment(Request $request, Response $response, int $comment_id): Response
    {
        return $response;
    }

    /**
     * 删除指定评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function deleteComment(Request $request, Response $response, int $comment_id): Response
    {
        return $response;
    }
}
