<?php

declare(strict_types=1);

namespace MDClub\Route;

use MDClub\Constant\RouteNameConstant;
use MDClub\Controller\Rss\Article;
use MDClub\Controller\Rss\Question;
use MDClub\Controller\Rss\Topic;
use MDClub\Controller\Rss\User;
use MDClub\Initializer\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * RSS 路由
 */
class Rss
{
    public function __construct()
    {
        $slim = App::$slim;

        $slim->group('/rss', function (RouteCollectorProxy $group) {
            $group
                ->get('/questions', Question::class . ':getList')
                ->setName(RouteNameConstant::RSS_QUESTIONS);

            $group
                ->get('/questions/{question_id:\d+}/answers', Question::class . ':getAnswers')
                ->setName(RouteNameConstant::RSS_QUESTION_ANSWERS);

            $group
                ->get('/articles', Article::class . ':getList')
                ->setName(RouteNameConstant::RSS_ARTICLES);

            $group
                ->get('/users/{user_id:\d+}/questions', User::class . ':getQuestions')
                ->setName(RouteNameConstant::RSS_USER_QUESTIONS);

            $group
                ->get('/users/{user_id:\d+}/articles', User::class . ':getArticles')
                ->setName(RouteNameConstant::RSS_USER_ARTICLES);

            $group
                ->get('/users/{user_id:\d+}/answers', User::class . ':getAnswers')
                ->setName(RouteNameConstant::RSS_USER_ANSWERS);

            $group
                ->get('/topics/{topic_id:\d+}/questions', Topic::class . ':getQuestions')
                ->setName(RouteNameConstant::RSS_TOPIC_QUESTIONS);

            $group
                ->get('/topics/{topic_id:\d+}/articles', Topic::class . ':getArticles')
                ->setName(RouteNameConstant::RSS_TOPIC_ARTICLES);
        });
    }
}
