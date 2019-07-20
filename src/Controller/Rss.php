<?php

declare(strict_types=1);

namespace MDClub\Controller;

use Psr\Http\Message\ResponseInterface;

/**
 * 输出 RSS
 */
class Rss extends Abstracts
{
    /**
     * 获取提问列表
     *
     * @return ResponseInterface
     */
    public function getQuestions(): ResponseInterface
    {

    }

    /**
     * 获取文章列表
     *
     * @return ResponseInterface
     */
    public function getArticles(): ResponseInterface
    {

    }

    /**
     * 根据 user_id 获取提问列表
     *
     * @param  int      $user_id
     * @return ResponseInterface
     */
    public function getQuestionsByUserId(int $user_id): ResponseInterface
    {

    }

    /**
     * 根据 user_id 获取文章列表
     *
     * @param  int      $user_id
     * @return ResponseInterface
     */
    public function getArticlesByUserId(int $user_id): ResponseInterface
    {

    }

    /**
     * 根据 topic_id 获取提问列表
     *
     * @param  int      $topic_id
     * @return ResponseInterface
     */
    public function getQuestionsByTopicId(int $topic_id): ResponseInterface
    {

    }

    /**
     * 根据 topic_id 获取文章列表
     *
     * @param  int      $topic_id
     * @return ResponseInterface
     */
    public function getArticlesByTopicId(int $topic_id): ResponseInterface
    {

    }

    /**
     * 根据 user_id 获取回答列表
     *
     * @param  int      $user_id
     * @return ResponseInterface
     */
    public function getAnswersByUserId(int $user_id): ResponseInterface
    {

    }

    /**
     * 根据 question_id 获取回答列表
     *
     * @param  int      $question_id
     * @return ResponseInterface
     */
    public function getAnswersByQuestionId(int $question_id): ResponseInterface
    {

    }
}
