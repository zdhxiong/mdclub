<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 输出 RSS
 */
class Rss extends ContainerAbstracts
{
    /**
     * 获取提问列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getQuestions(Request $request, Response $response): Response
    {

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

    }

    /**
     * 根据 user_id 获取提问列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getQuestionsByUserId(Request $request, Response $response, int $user_id): Response
    {

    }

    /**
     * 根据 user_id 获取文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getArticlesByUserId(Request $request, Response $response, int $user_id): Response
    {

    }

    /**
     * 根据 topic_id 获取提问列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function getQuestionsByTopicId(Request $request, Response $response, int $topic_id): Response
    {

    }

    /**
     * 根据 topic_id 获取文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function getArticlesByTopicId(Request $request, Response $response, int $topic_id): Response
    {

    }

    /**
     * 根据 user_id 获取回答列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getAnswersByUserId(Request $request, Response $response, int $user_id): Response
    {

    }

    /**
     * 根据 question_id 获取回答列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function getAnswersByQuestionId(Request $request, Response $response, int $question_id): Response
    {

    }
}
