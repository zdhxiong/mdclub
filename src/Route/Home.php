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
use Slim\App;

/**
 * Home 页面路由
 */
class Home
{
    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        /**
         * 首页
         *
         * @see Index::index()
         */
        $app->get('/', Index::class . ':index');

        /**
         * 数据库迁移，临时使用
         *
         * @see Index::migration()
         */
        $app->get('/migration', Index::class . ':migration');

        /**
         * 话题列表页
         *
         * @see Topic::index()
         */
        $app->get('/topics', Topic::class . ':index');

        /**
         * 话题详情页
         *
         * @see Topic::info()
         */
        $app->get('/topics/{topic_id:\d+}', Topic::class . ':info');

        /**
         * 文章列表页
         *
         * @see Article::index()
         */
        $app->get('/articles', Article::class . ':index');

        /**
         * 文章详情页
         *
         * @see Article::info()
         */
        $app->get('/articles/{article_id:\d+}', Article::class . ':info');

        /**
         * 提问列表页
         *
         * @see Question::index()
         */
        $app->get('/questions', Question::class . ':index');

        /**
         * 提问详情页
         *
         * @see Question::info()
         */
        $app->get('/questions/{question_id:\d+}', Question::class . ':info');

        /**
         * 用户列表页
         *
         * @see User::index()
         */
        $app->get('/users', User::class . ':index');

        /**
         * 用户详情页
         *
         * @see User::info()
         */
        $app->get('/users/{user_id:\d+}', User::class . ':info');

        /**
         * 通知列表页
         *
         * @see Notification::index()
         */
        $app->get('/notifications', Notification::class . ':index');

        /**
         * 私信列表页
         *
         * @see Inbox::index()
         */
        $app->get('/inbox', Inbox::class . ':index');

        /**
         * API 首页
         *
         * @see Api::index()
         */
        $app->get('/api', Api::class . ':index');
    }
}
