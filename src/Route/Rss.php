<?php

declare(strict_types=1);

namespace MDClub\Route;

use MDClub\Controller\Rss\Article;
use MDClub\Controller\Rss\Question;
use MDClub\Controller\Rss\Topic;
use MDClub\Controller\Rss\User;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * Rss 路由
 */
class Rss
{
    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $app->group('/rss', function (RouteCollectorProxy $group) {
            /**
             * 提问列表 RSS
             *
             * @see Question::getList()
             */
            $group->get('/questions', Question::class . ':getList');

            /**
             * 文章列表 RSS
             *
             * @see Article::getList()
             */
            $group->get('/articles', Article::class . ':getList');

            /**
             * 指定用户发表的提问列表 RSS
             *
             * @see User::getQuestions()
             */
            $group->get('/users/{user_id:\d+}/questions', User::class . ':getQuestions');

            /**
             * 指定用户发表的文章列表 RSS
             *
             * @see User::getArticles()
             */
            $group->get('/users/{user_id:\d+}/articles', User::class . ':getArticles');

            /**
             * 指定话题下的提问列表 RSS
             *
             * @see Topic::getQuestions()
             */
            $group->get('/topics/{topic_id:\d+}/questions', Topic::class . ':getQuestions');

            /**
             * 获取指定话题下的文章列表 RSS
             *
             * @see Topic::getArticles()
             */
            $group->get('/topics/{topic_id:\d+}/articles', Topic::class . ':getArticles');

            /**
             * 获取指定用户发表的回答 RSS
             *
             * @see User::getAnswers()
             */
            $group->get('/users/{user_id:\d+}/answers', User::class . ':getAnswers');

            /**
             * 获取指定提问下的回答 RSS
             *
             * @see Question::getAnswers()
             */
            $group->get('/questions/{question_id:\d+}/answers', Question::class . ':getAnswers');
        });
    }
}
