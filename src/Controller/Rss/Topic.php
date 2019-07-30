<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use MDClub\Controller\Abstracts;
use Psr\Http\Message\ResponseInterface;

class Topic extends Abstracts
{
    /**
     * 根据 topic_id 获取提问列表
     *
     * @param  int               $topic_id
     * @return ResponseInterface
     */
    public function getQuestions(int $topic_id): ResponseInterface
    {

    }

    /**
     * 根据 topic_id 获取文章列表
     *
     * @param  int               $topic_id
     * @return ResponseInterface
     */
    public function getArticles(int $topic_id): ResponseInterface
    {

    }
}
