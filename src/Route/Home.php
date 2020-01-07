<?php

declare(strict_types=1);

namespace MDClub\Route;

use MDClub\Controller\Home\Api;
use MDClub\Controller\Home\Article;
use MDClub\Controller\Home\Inbox;
use MDClub\Controller\Home\Index;
use MDClub\Controller\Home\Notification;
use MDClub\Controller\Home\Question;
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

        $slim->get('/', Index::class . ':index');

        $slim->get('/migration', Index::class . ':migration');
        $slim->get('/topics', Topic::class . ':index');
        $slim->get('/topics/{topic_id:\d+}', Topic::class . ':info');
        $slim->get('/articles', Article::class . ':index');
        $slim->get('/articles/{article_id:\d+}', Article::class . ':info');
        $slim->get('/questions', Question::class . ':index');
        $slim->get('/questions/{question_id:\d+}', Question::class . ':info');
        $slim->get('/users', User::class . ':index');
        $slim->get('/users/{user_id:\d+}', User::class . ':info');
        $slim->get('/notifications', Notification::class . ':index');
        $slim->get('/inbox', Inbox::class . ':index');
        $slim->get('/api', Api::class . ':index');
    }
}
