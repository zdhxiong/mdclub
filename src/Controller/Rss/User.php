<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use Psr\Http\Message\ResponseInterface;

/**
 * 用户 RSS
 */
class User
{
    /**
     * 指定用户的提问列表 RSS
     *
     * @param int $userId
     *
     * @return ResponseInterface
     */
    public function getQuestions(int $userId): ResponseInterface
    {
    }

    /**
     * 指定用户的文章列表 RSS
     *
     * @param int $userId
     *
     * @return ResponseInterface
     */
    public function getArticles(int $userId): ResponseInterface
    {
    }

    /**
     * 根据用户的回答列表 RSS
     *
     * @param int $userId
     *
     * @return ResponseInterface
     */
    public function getAnswers(int $userId): ResponseInterface
    {
    }
}
