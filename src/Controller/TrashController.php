<?php

declare(strict_types=1);

namespace App\Controller;


use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 回收站
 *
 * Class TrashController
 * @package App\Controller
 */
class TrashController extends Controller
{
    /**
     * 清空回收站
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function deleteAll(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 获取用户列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getUsers(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 恢复指定用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function restoreUser(Request $request, Response $response, int $user_id): Response
    {
        return $response;
    }

    /**
     * 获取话题列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getTopics(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 恢复指定话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function restoreTopic(Request $request, Response $response, int $topic_id): Response
    {
        return $response;
    }

    /**
     * 删除指定话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function deleteTopic(Request $request, Response $response, int $topic_id): Response
    {
        return $response;
    }

    /**
     * 获取问题列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getQuestions(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 恢复指定问题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function restoreQuestion(Request $request, Response $response, int $question_id): Response
    {
        return $response;
    }

    /**
     * 删除指定问题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function deleteQuestion(Request $request, Response $response, int $question_id): Response
    {
        return $response;
    }

    /**
     * 获取回答列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getAnswers(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 恢复指定回答
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function restoreAnswer(Request $request, Response $response, int $answer_id): Response
    {
        return $response;
    }

    /**
     * 删除指定回答
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function deleteAnswer(Request $request, Response $response, int $answer_id): Response
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
    public function getArticles(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 恢复指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function restoreArticle(Request $request, Response $response, int $article_id): Response
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
    public function deleteArticle(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }

    /**
     * 获取评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getComments(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 恢复指定评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function restoreComment(Request $request, Response $response, int $comment_id): Response
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
