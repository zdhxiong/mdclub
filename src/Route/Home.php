<?php

declare(strict_types=1);

namespace MDClub\Route;

use MDClub\Constant\RouteNameConstant;
use MDClub\Controller\Home\Api;
use MDClub\Controller\Home\Article;
use MDClub\Controller\Home\Inbox;
use MDClub\Controller\Home\Index;
use MDClub\Controller\Home\Notification;
use MDClub\Controller\Home\Question;
use MDClub\Controller\Home\Sitemap;
use MDClub\Controller\Home\Topic;
use MDClub\Controller\Home\User;
use MDClub\Initializer\App;

/**
 * Home 页面路由
 */
class Home
{
    public function __construct()
    {
        $slim = App::$slim;

        $slim
            ->get('/', Index::class . ':index')
            ->setName(RouteNameConstant::INDEX);

        $slim
            ->get('/topics', Topic::class . ':index')
            ->setName(RouteNameConstant::TOPICS);

        $slim
            ->get('/topics/{topic_id:\d+}', Topic::class . ':info')
            ->setName(RouteNameConstant::TOPIC);

        $slim
            ->get('/articles', Article::class . ':index')
            ->setName(RouteNameConstant::ARTICLES);

        $slim
            ->get('/articles/{article_id:\d+}', Article::class . ':info')
            ->setName(RouteNameConstant::ARTICLE);

        $slim
            ->get('/questions', Question::class . ':index')
            ->setName(RouteNameConstant::QUESTIONS);

        $slim
            ->get('/questions/{question_id:\d+}', Question::class . ':info')
            ->setName(RouteNameConstant::QUESTION);

        $slim
            ->get('/questions/{question_id:\d+}/answers/{answer_id:\d+}', Question::class . ':answer')
            ->setName(RouteNameConstant::ANSWER);

        $slim
            ->get('/users', User::class . ':index')
            ->setName(RouteNameConstant::USERS);

        $slim
            ->get('/users/{user_id:\d+}', User::class . ':info')
            ->setName(RouteNameConstant::USER);

        $slim
            ->get('/notifications', Notification::class . ':index');

        $slim
            ->get('/inbox', Inbox::class . ':index');

        $slim
            ->get('/api', Api::class . ':index');

        $slim
            ->get('/sitemap.xml', Sitemap::class . ':index');
    }
}
