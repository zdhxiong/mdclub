<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use MDClub\Controller\Abstracts;
use Psr\Http\Message\ResponseInterface;

class Question extends Abstracts
{
    /**
     * 获取提问列表
     *
     * @return ResponseInterface
     */
    public function getList(): ResponseInterface
    {

    }

    /**
     * 根据 question_id 获取回答列表
     *
     * @param  int               $question_id
     * @return ResponseInterface
     */
    public function getAnswers(int $question_id): ResponseInterface
    {

    }
}
