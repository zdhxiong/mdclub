<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use Psr\Http\Message\ResponseInterface;

/**
 * 话题 RSS
 */
class Topic
{
    /**
     * 指定话题下的提问列表 RSS
     *
     * @param int $topicId
     *
     * @return ResponseInterface
     */
    public function getQuestions(int $topicId): ResponseInterface
    {
    }

    /**
     * 指定话题下的文章列表 RSS
     *
     * @param int $topicId
     *
     * @return ResponseInterface
     */
    public function getArticles(int $topicId): ResponseInterface
    {
    }
}
