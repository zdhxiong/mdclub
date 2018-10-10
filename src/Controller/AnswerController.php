<?php

declare(strict_types=1);

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 回答
 *
 * Class AnswerController
 * @package App\Controller
 */
class AnswerController extends Controller
{
    /**
     * 获取指定问题下的回答列表
     *
     * @param Request $request
     * @param Response $response
     * @param int $question_id
     *
     * @return Response
     */
    public function getListByQuestionId(Request $request, Response $response, int $question_id): Response
    {
        return $response;
    }

    /**
     * 在指定问题下创建回答
     *
     * @param Request $request
     * @param Response $response
     * @param int $question_id
     *
     * @return Response
     */
    public function create(Request $request, Response $response, int $question_id): Response
    {
        return $response;
    }

    /**
     * 获取指定回答的详情
     *
     * @param Request $request
     * @param Response $response
     * @param int $answer_id
     *
     * @return Response
     */
    public function getDetail(Request $request, Response $response, int $answer_id): Response
    {
        return $response;
    }

    /**
     * 获取回答列表
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        return $response;
    }
}
