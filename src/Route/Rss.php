<?php

declare(strict_types=1);

namespace MDClub\Route;

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
            $group->get('/questions', Question::class . ':getList');
            $group->get('/articles', Article::class . ':getList');
            $group->get('/users/{user_id:\d+}/questions', User::class . ':getQuestions');
            $group->get('/users/{user_id:\d+}/articles', User::class . ':getArticles');
            $group->get('/topics/{topic_id:\d+}/questions', Topic::class . ':getQuestions');
            $group->get('/topics/{topic_id:\d+}/articles', Topic::class . ':getArticles');
            $group->get('/users/{user_id:\d+}/answers', User::class . ':getAnswers');
            $group->get('/questions/{question_id:\d+}/answers', Question::class . ':getAnswers');
        });
    }
}
