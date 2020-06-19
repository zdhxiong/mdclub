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
use MDClub\Middleware\NeedInstalled;

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
            ->setName(RouteNameConstant::INDEX)
            ->add(NeedInstalled::class);

        $slim
            ->get('/topics', Topic::class . ':index')
            ->setName(RouteNameConstant::TOPICS)
            ->add(NeedInstalled::class);

        $slim
            ->get('/topics/{topic_id:\d+}', Topic::class . ':info')
            ->setName(RouteNameConstant::TOPIC)
            ->add(NeedInstalled::class);

        $slim
            ->get('/articles', Article::class . ':index')
            ->setName(RouteNameConstant::ARTICLES)
            ->add(NeedInstalled::class);

        $slim
            ->get('/articles/{article_id:\d+}', Article::class . ':info')
            ->setName(RouteNameConstant::ARTICLE)
            ->add(NeedInstalled::class);

        $slim
            ->get('/questions', Question::class . ':index')
            ->setName(RouteNameConstant::QUESTIONS)
            ->add(NeedInstalled::class);

        $slim
            ->get('/questions/{question_id:\d+}', Question::class . ':info')
            ->setName(RouteNameConstant::QUESTION)
            ->add(NeedInstalled::class);

        $slim
            ->get('/questions/{question_id:\d+}/answers/{answer_id:\d+}', Question::class . ':answer')
            ->setName(RouteNameConstant::ANSWER)
            ->add(NeedInstalled::class);

        $slim
            ->get('/users', User::class . ':index')
            ->setName(RouteNameConstant::USERS)
            ->add(NeedInstalled::class);

        $slim
            ->get('/users/{user_id:\d+}', User::class . ':info')
            ->setName(RouteNameConstant::USER)
            ->add(NeedInstalled::class);

        $slim
            ->get('/notifications', Notification::class . ':index')
            ->setName(RouteNameConstant::NOTIFICATIONS)
            ->add(NeedInstalled::class);

        $slim
            ->get('/inbox', Inbox::class . ':index')
            ->add(NeedInstalled::class);

        if (App::$config['APP_SHOW_API_DOCS']) {
            $slim
                ->get('/api', Api::class . ':index')
                ->add(NeedInstalled::class);
        }

        $slim
            ->get('/sitemap.xml', Sitemap::class . ':index')
            ->add(NeedInstalled::class);
    }
}
