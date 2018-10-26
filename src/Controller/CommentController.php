<?php

declare(strict_types=1);

namespace App\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 评论
 *
 * Class CommentController
 * @package App\Controller
 */
class CommentController extends Controller
{
    /**
     * 获取所有评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        return $response;
    }
}
