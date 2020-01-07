<?php

declare(strict_types=1);

namespace MDClub\Controller\Rss;

use Psr\Http\Message\ResponseInterface;

/**
 * 提问 RSS
 */
class Question
{
    /**
     * 提问列表 RSS
     *
     * @return ResponseInterface
     */
    public function getList(): ResponseInterface
    {
    }

    /**
     * 某一提问的回答 RSS
     *
     * @param int $questionId
     *
     * @return ResponseInterface
     */
    public function getAnswers(int $questionId): ResponseInterface
    {
    }
}
