<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use MDClub\Controller\Abstracts;
use Psr\Http\Message\ResponseInterface;

class User extends Abstracts
{
    /**
     * 根据 user_id 获取提问列表
     *
     * @param  int               $user_id
     * @return ResponseInterface
     */
    public function getQuestions(int $user_id): ResponseInterface
    {

    }

    /**
     * 根据 user_id 获取文章列表
     *
     * @param  int               $user_id
     * @return ResponseInterface
     */
    public function getArticles(int $user_id): ResponseInterface
    {

    }

    /**
     * 根据 user_id 获取回答列表
     *
     * @param  int               $user_id
     * @return ResponseInterface
     */
    public function getAnswers(int $user_id): ResponseInterface
    {

    }
}
