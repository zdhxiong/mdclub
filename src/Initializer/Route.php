<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use MDClub\Middleware\EnableCrossRequest;
use MDClub\Middleware\JsonBodyParser;
use MDClub\Middleware\NeedLogin;
use MDClub\Middleware\NeedManager;
use MDClub\Middleware\TransformAnswer;
use MDClub\Middleware\TransformArticle;
use MDClub\Middleware\TransformComment;
use MDClub\Middleware\TransformImage;
use MDClub\Middleware\TransformQuestion;
use MDClub\Middleware\TransformTopic;
use MDClub\Middleware\TransformUser;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * 路由
 */
class Route
{
    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        $container = $app->getContainer();
        $routeCollector = $app->getRouteCollector();
        $debug = $container->get('settings')['debug'];

        // 生产模式启用路由缓存
        if (!$debug) {
            $routeCollector->setCacheFile(__DIR__ . '/../../var/cache/CompiledRoute.php');
        }

        // 设置路由回调策略
        $routeCollector->setDefaultInvocationStrategy(new RouteStrategy($container));

        $route = $this;

        $route->home($app);

        $app->group('/api', function (RouteCollectorProxy $group) use ($route) {
            $route->indexApi($group);
            $route->tokenApi($group);
            $route->optionApi($group);
            $route->userApi($group);
            $route->topicApi($group);
            $route->questionApi($group);
            $route->answerApi($group);
            $route->articleApi($group);
            $route->commentApi($group);
            $route->reportApi($group);
            $route->inboxApi($group);
            $route->notificationApi($group);
            $route->captchaApi($group);
            $route->emailApi($group);
            $route->imageApi($group);
        })->add(EnableCrossRequest::class)
            ->add(JsonBodyParser::class);

        $app->group('/rss', function (RouteCollectorProxy $group) use ($route) {
            $route->rss($group);
        });

        $app->group('/admin', function (RouteCollectorProxy $group) use ($route) {
            $route->admin($group);
        });
    }

    /**
     * HTML 页面路由
     *
     * @param App $app
     */
    public function home(App $app): void
    {
        /**
         * 首页
         *
         * @see Index::pageIndex()
         */
        $app->get('/', 'Index/pageIndex');

        /**
         * 数据库迁移，临时使用
         *
         * @see Index::migration()
         */
        $app->get('/migration', 'Index/migration');

        /**
         * 话题列表页
         *
         * @see Topic::pageIndex()
         */
        $app->get('/topics', 'Topic/pageIndex');

        /**
         * 话题详情页
         *
         * @see Topic::pageInfo()
         */
        $app->get('/topics/{topic_id:\d+}', 'Topic/pageInfo');

        /**
         * 文章列表页
         *
         * @see Article::pageIndex()
         */
        $app->get('/articles', 'Article/pageIndex');

        /**
         * 文章详情页
         *
         * @see Article::pageInfo()
         */
        $app->get('/articles/{article_id:\d+}', 'Article/pageInfo');

        /**
         * 提问列表页
         *
         * @see Question::pageIndex()
         */
        $app->get('/questions', 'Question/pageIndex');

        /**
         * 提问详情页
         *
         * @see Question::pageInfo()
         */
        $app->get('/questions/{question_id:\d+}', 'Question/pageInfo');

        /**
         * 用户列表页
         *
         * @see User::pageIndex()
         */
        $app->get('/users', 'User/pageIndex');

        /**
         * 用户详情页
         *
         * @see User::pageInfo()
         */
        $app->get('/users/{user_id:\d+}', 'User/pageInfo');

        /**
         * 通知列表页
         *
         * @see Notification::pageIndex()
         */
        $app->get('/notifications', 'Notification/pageIndex');

        /**
         * 私信列表页
         *
         * @see Inbox::pageIndex()
         */
        $app->get('/inbox', 'Inbox/pageIndex');
    }

    /**
     * RSS
     *
     * @param RouteCollectorProxy $group
     */
    public function rss(RouteCollectorProxy $group): void
    {
        /**
         * 提问列表 RSS
         *
         * @see Rss::getQuestions()
         */
        $group->get('/questions', 'Rss/getQuestions');

        /**
         * 文章列表 RSS
         *
         * @see Rss::getArticles()
         */
        $group->get('/articles', 'Rss/getArticles');

        /**
         * 指定用户发表的提问列表 RSS
         *
         * @see Rss::getQuestionsByUserId()
         */
        $group->get('/users/{user_id:\d+}/questions', 'Rss/getQuestionsByUserId');

        /**
         * 指定用户发表的文章列表 RSS
         *
         * @see Rss::getArticlesByUserId()
         */
        $group->get('/users/{user_id:\d+}/articles', 'Rss/getArticlesByUserId');

        /**
         * 指定话题下的提问列表 RSS
         *
         * @see Rss::getQuestionsByTopicId()
         */
        $group->get('/topics/{topic_id:\d+}/questions', 'Rss/getQuestionsByTopicId');

        /**
         * 获取指定话题下的文章列表 RSS
         *
         * @see Rss::getArticlesByTopicId()
         */
        $group->get('/topics/{topic_id:\d+}/articles', 'Rss/getArticlesByTopicId');

        /**
         * 获取指定用户发表的回答 RSS
         *
         * @see Rss::getAnswersByUserId()
         */
        $group->get('/users/{user_id:\d+}/answers', 'Rss/getAnswersByUserId');

        /**
         * 获取指定提问下的回答 RSS
         *
         * @see Rss::getAnswersByQuestionId()
         */
        $group->get('/questions/{question_id:\d+}/answers', 'Rss/getAnswersByQuestionId');
    }

    /**
     * admin
     *
     * @param RouteCollectorProxy $group
     */
    public function admin(RouteCollectorProxy $group): void
    {
        $group->get('', 'Admin/pageIndex'); /** @see Admin::pageIndex() */
        $group->get('/{name}', 'Admin/pageIndex'); /** @see Admin::pageIndex() */
        $group->get('/{name}/{sub_name}', 'Admin/pageIndex'); /** @see Admin::pageIndex() */
    }

    /**
     * API 首页
     *
     * @param RouteCollectorProxy $group
     */
    public function indexApi(RouteCollectorProxy $group): void
    {
        /**
         * API 首页
         *
         * @see Api::pageIndex()
         */
        $group->get('', 'Api/pageIndex');
    }

    /**
     * 身份验证
     *
     * @param RouteCollectorProxy $group
     */
    public function tokenApi(RouteCollectorProxy $group): void
    {
        /**
         * 生成 Token
         *
         * @see TokenApi::create()
         */
        $group->post('/tokens', 'TokenApi/create');
    }

    /**
     * 系统设置
     *
     * @param RouteCollectorProxy $group
     */
    public function optionApi(RouteCollectorProxy $group): void
    {
        /**
         * 获取设置
         *
         * @see OptionApi::get()
         */
        $group->get('/options', 'OptionApi/get');

        /**
         * 更新设置
         *
         * @see OptionApi::update()
         */
        $group->patch('/options', 'OptionApi/update')
            ->add(NeedManager::class);
    }

    /**
     * 用户
     *
     * @param RouteCollectorProxy $group
     */
    public function userApi(RouteCollectorProxy $group): void
    {
        /**
         * 获取用户列表
         *
         * @see UserApi::getList()
         */
        $group->get('/users', 'UserApi/getList')
            ->add(TransformUser::class);

        /**
         * 注册账户
         *
         * @see UserApi::register()
         */
        $group->post('/users', 'UserApi/register');

        /**
         * 批量禁用用户
         *
         * @see UserApi::disableMultiple()
         */
        $group->delete('/users', 'UserApi/disableMultiple')
            ->add(NeedManager::class);

        /**
         * 获取指定用户信息
         *
         * @see UserApi::get()
         */
        $group->get('/users/{user_id:\d+}', 'UserApi/get')
            ->add(TransformUser::class);

        /**
         * 更新指定用户信息
         *
         * @see UserApi::update()
         */
        $group->patch('/users/{user_id:\d+}', 'UserApi/update')
            ->add(NeedManager::class);

        /**
         * 禁用指定用户
         *
         * @see UserApi::disable()
         */
        $group->delete('/users/{user_id:\d+}', 'UserApi/disable')
            ->add(NeedManager::class);

        /**
         * 删除指定用户的头像
         *
         * @see UserApi::deleteAvatar()
         */
        $group->delete('/users/{user_id:\d+}/avatar', 'UserApi/deleteAvatar')
            ->add(NeedManager::class);

        /**
         * 删除指定用户的封面
         *
         * @see UserApi::deleteCover()
         */
        $group->delete('/users/{user_id:\d+}/cover', 'UserApi/deleteCover')
            ->add(NeedManager::class);

        /**
         * 获取指定用户的关注者
         *
         * @see UserApi::getFollowers()
         */
        $group->get('/users/{user_id:\d+}/followers', 'UserApi/getFollowers')
            ->add(TransformUser::class);

        /**
         * 关注指定用户
         *
         * @see UserApi::addFollow()
         */
        $group->post('/users/{user_id:\d+}/followers', 'UserApi/addFollow')
            ->add(NeedLogin::class);

        /**
         * 取消关注指定用户
         *
         * @see UserApi::deleteFollow()
         */
        $group->delete('/users/{user_id:\d+}/followers', 'UserApi/deleteFollow')
            ->add(NeedLogin::class);

        /**
         * 获取指定用户关注的用户
         *
         * @see UserApi::getFollowees()
         */
        $group->get('/users/{user_id:\d+}/followees', 'UserApi/getFollowees')
            ->add(TransformUser::class);

        /**
         * 获取指定用户关注的提问
         *
         * @see UserApi::getFollowingQuestions()
         */
        $group->get('/users/{user_id:\d+}/following_questions', 'UserApi/getFollowingQuestions')
            ->add(TransformQuestion::class);

        /**
         * 获取指定用户关注的文章
         *
         * @see UserApi::getFollowingArticles()
         */
        $group->get('/users/{user_id:\d+}/following_articles', 'UserApi/getFollowingArticles')
            ->add(TransformArticle::class);

        /**
         * 获取指定用户关注的话题
         *
         * @see UserApi::getFollowingTopics()
         */
        $group->get('/users/{user_id:\d+}/following_topics', 'UserApi/getFollowingTopics')
            ->add(TransformTopic::class);

        /**
         * 获取指定用户发表的提问
         *
         * @see UserApi::getQuestions()
         */
        $group->get('/users/{user_id:\d+}/questions', 'UserApi/getQuestions')
            ->add(TransformQuestion::class);

        /**
         * 获取指定用户发表的回答
         *
         * @see UserApi::getAnswers()
         */
        $group->get('/users/{user_id:\d+}/answers', 'UserApi/getAnswers')
            ->add(TransformAnswer::class);

        /**
         * 获取指定用户发表的文章
         *
         * @see UserApi::getArticles()
         */
        $group->get('/users/{user_id:\d+}/articles', 'UserApi/getArticles')
            ->add(TransformArticle::class);

        /**
         * 获取指定用户发表的评论
         *
         * @see UserApi::getComments()
         */
        $group->get('/users/{user_id:\d+}/comments', 'UserApi/getComments')
            ->add(TransformComment::class);

        /**
         * 获取当前用户信息
         *
         * @see UserApi::getMine()
         */
        $group->get('/user', 'UserApi/getMine')
            ->add(NeedLogin::class)
            ->add(TransformUser::class);

        /**
         * 更新当前用户信息
         *
         * @see UserApi::updateMine()
         */
        $group->patch('/user', 'UserApi/updateMine')
            ->add(NeedLogin::class)
            ->add(TransformUser::class);

        /**
         * 上传当前用户头像
         *
         * @see UserApi::uploadMyAvatar()
         */
        $group->post('/user/avatar', 'UserApi/uploadMyAvatar')
            ->add(NeedLogin::class);

        /**
         * 删除当前用户头像
         *
         * @see UserApi::deleteMyAvatar()
         */
        $group->delete('/user/avatar', 'UserApi/deleteMyAvatar')
            ->add(NeedLogin::class);

        /**
         * 上传当前用户封面
         *
         * @see UserApi::uploadMyCover()
         */
        $group->post('/user/cover', 'UserApi/uploadMyCover')
            ->add(NeedLogin::class);

        /**
         * 删除当前用户封面
         *
         * @see UserApi::deleteMyCover()
         */
        $group->delete('/user/cover', 'UserApi/deleteMyCover')
            ->add(NeedLogin::class);

        /**
         * 发送注册验证邮件
         *
         * @see UserApi::sendRegisterEmail()
         */
        $group->post('/user/register/email', 'UserApi/sendRegisterEmail');

        /**
         * 发送重置密码验证邮件
         *
         * @see UserApi::sendPasswordResetEmail()
         */
        $group->post('/user/password/email', 'UserApi/sendPasswordResetEmail');

        /**
         * 重置密码
         *
         * @see UserApi::updatePassword()
         */
        $group->put('/user/password', 'UserApi/updatePassword');

        /**
         * 获取当前用户的关注者
         *
         * @see UserApi::getMyFollowers()
         */
        $group->get('/user/followers', 'UserApi/getMyFollowers')
            ->add(NeedLogin::class)
            ->add(TransformUser::class);

        /**
         * 获取当前用户关注的用户
         *
         * @see UserApi::getMyFollowees()
         */
        $group->get('/user/followees', 'UserApi/getMyFollowees')
            ->add(NeedLogin::class)
            ->add(TransformUser::class);

        /**
         * 获取当前用户关注的提问
         *
         * @see UserApi::getMyFollowingQuestions()
         */
        $group->get('/user/following_questions', 'UserApi/getMyFollowingQuestions')
            ->add(NeedLogin::class)
            ->add(TransformQuestion::class);

        /**
         * 获取当前用户关注的文章
         *
         * @see UserApi::getMyFollowingArticles()
         */
        $group->get('/user/following_articles', 'UserApi/getMyFollowingArticles')
            ->add(NeedLogin::class)
            ->add(TransformArticle::class);

        /**
         * 获取当前用户关注的话题
         *
         * @see UserApi::getMyFollowingTopics()
         */
        $group->get('/user/following_topics', 'UserApi/getMyFollowingTopics')
            ->add(NeedLogin::class)
            ->add(TransformTopic::class);

        /**
         * 获取当前用户发表的提问
         *
         * @see UserApi::getMyQuestions()
         */
        $group->get('/user/questions', 'UserApi/getMyQuestions')
            ->add(NeedLogin::class)
            ->add(TransformQuestion::class);

        /**
         * 获取当前用户发表的回答
         *
         * @see UserApi::getMyAnswers()
         */
        $group->get('/user/answers', 'UserApi/getMyAnswers')
            ->add(NeedLogin::class)
            ->add(TransformAnswer::class);

        /**
         * 获取当前用户发表的文章
         *
         * @see UserApi::getMyArticles()
         */
        $group->get('/user/articles', 'UserApi/getMyArticles')
            ->add(NeedLogin::class)
            ->add(TransformArticle::class);

        /**
         * 获取当前用户发表的评论
         *
         * @see UserApi::getMyComments()
         */
        $group->get('/user/comments', 'UserApi/getMyComments')
            ->add(NeedLogin::class)
            ->add(TransformComment::class);

        /**
         * 获取已禁用的用户列表
         *
         * @see UserApi::getDisabled()
         */
        $group->get('/trash/users', 'UserApi/getDisabled')
            ->add(NeedManager::class)
            ->add(TransformUser::class);

        /**
         * 批量启用用户
         *
         * @see UserApi::enableMultiple()
         */
        $group->post('/trash/users', 'UserApi/enableMultiple')
            ->add(NeedManager::class);

        /**
         * 启用用户
         *
         * @see UserApi::enable()
         */
        $group->post('/trash/users/{user_id:\d+}', 'UserApi/enable')
            ->add(NeedManager::class);
    }

    /**
     * 话题
     *
     * @param RouteCollectorProxy $group
     */
    public function topicApi(RouteCollectorProxy $group): void
    {
        /**
         * 获取话题列表
         *
         * @see TopicApi::getList()
         */
        $group->get('/topics', 'TopicApi/getList')
            ->add(TransformTopic::class);

        /**
         * 发表话题
         *
         * @see TopicApi::create()
         */
        $group->post('/topics', 'TopicApi/create')
            ->add(NeedManager::class)
            ->add(TransformTopic::class);

        /**
         * 批量删除话题
         *
         * @see TopicApi::deleteMultiple()
         */
        $group->delete('/topics', 'TopicApi/deleteMultiple')
            ->add(NeedManager::class);

        /**
         * 获取指定话题
         *
         * @see TopicApi::get()
         */
        $group->get('/topics/{topic_id:\d+}', 'TopicApi/get')
            ->add(TransformTopic::class);

        /**
         * 更新话题
         *
         * NOTE: formData 数据只能通过 post 请求提交，所以这里不用 patch 请求
         *
         * @see TopicApi::update()
         */
        $group->post('/topics/{topic_id:\d+}', 'TopicApi/update')
            ->add(NeedManager::class)
            ->add(TransformTopic::class);

        /**
         * 删除话题
         *
         * @see TopicApi::delete()
         */
        $group->delete('/topics/{topic_id:\d+}', 'TopicApi/delete');

        /**
         * 获取指定话题的关注者
         *
         * @see TopicApi::getFollowers()
         */
        $group->get('/topics/{topic_id:\d+}/followers', 'TopicApi/getFollowers')
            ->add(TransformUser::class);

        /**
         * 关注指定话题
         *
         * @see TopicApi::addFollow()
         */
        $group->post('/topics/{topic_id:\d+}/followers', 'TopicApi/addFollow')
            ->add(NeedLogin::class);

        /**
         * 取消关注指定话题
         *
         * @see TopicApi::deleteFollow()
         */
        $group->delete('/topics/{topic_id:\d+}/followers', 'TopicApi/deleteFollow')
            ->add(NeedLogin::class);

        /**
         * 获取指定话题下的提问
         *
         * @see TopicApi::getQuestions()
         */
        $group->get('/topics/{topic_id:\d+}/questions', 'TopicApi/getQuestions')
            ->add(TransformQuestion::class);

        /**
         * 获取指定话题下的文章
         *
         * @see TopicApi::getArticles()
         */
        $group->get('/topics/{topic_id:\d+}/articles', 'TopicApi/getArticles');

        /**
         * 获取回收站中的话题
         *
         * @see TopicApi::getDeleted()
         */
        $group->get('/trash/topics', 'TopicApi/getDeleted')
            ->add(NeedManager::class);

        /**
         * 批量恢复话题
         *
         * @see TopicApi::restoreMultiple()
         */
        $group->post('/trash/topics', 'TopicApi/restoreMultiple')
            ->add(NeedManager::class);

        /**
         * 批量销毁话题
         *
         * @see TopicApi::destroyMultiple()
         */
        $group->delete('/trash/topics', 'TopicApi/destroyMultiple')
            ->add(NeedManager::class);

        /**
         * 恢复话题
         *
         * @see TopicApi::restore()
         */
        $group->post('/trash/topics/{topic_id:\d+}', 'TopicApi/restore')
            ->add(NeedManager::class);

        /**
         * 删除话题
         *
         * @see TopicApi::destroy()
         */
        $group->delete('/trash/topics/{topic_id:\d+}', 'TopicApi/destroy')
            ->add(NeedManager::class);
    }

    /**
     * 提问
     *
     * @param RouteCollectorProxy $group
     */
    public function questionApi(RouteCollectorProxy $group): void
    {
        /**
         * 获取提问列表
         *
         * @see QuestionApi::getList()
         */
        $group->get('/questions', 'QuestionApi/getList')
            ->add(TransformQuestion::class);

        /**
         * 发表提问
         *
         * @see QuestionApi::create()
         */
        $group->post('/questions', 'QuestionApi/create');

        /**
         * 批量删除提问
         *
         * @see QuestionApi::deleteMultiple()
         */
        $group->delete('/questions', 'QuestionApi/deleteMultiple');

        /**
         * 获取提问
         *
         * @see QuestionApi::get()
         */
        $group->get('/questions/{question_id:\d+}', 'QuestionApi/get')
            ->add(TransformQuestion::class);

        /**
         * 更新提问
         *
         * @see QuestionApi::update()
         */
        $group->patch('/questions/{question_id:\d+}', 'QuestionApi/update');

        /**
         * 删除提问
         *
         * @see QuestionApi::delete()
         */
        $group->delete('/questions/{question_id:\d+}', 'QuestionApi/delete');

        /**
         * 获取指定提问的投票者
         *
         * @see QuestionApi::getVoters()
         */
        $group->get('/questions/{question_id:\d+}/voters', 'QuestionApi/getVoters')
            ->add(TransformUser::class);

        /**
         * 给指定提问投票
         *
         * @see QuestionApi::addVote()
         */
        $group->post('/questions/{question_id:\d+}/voters', 'QuestionApi/addVote')
            ->add(NeedLogin::class);

        /**
         * 取消给指定提问的投票
         *
         * @see QuestionApi::deleteVote()
         */
        $group->delete('/questions/{question_id:\d+}/voters', 'QuestionApi/deleteVote')
            ->add(NeedLogin::class);

        /**
         * 获取指定提问的关注者
         *
         * @see QuestionApi::getFollowers()
         */
        $group->get('/questions/{question_id:\d+}/followers', 'QuestionApi/getFollowers')
            ->add(TransformUser::class);

        /**
         * 关注指定提问
         *
         * @see QuestionApi::addFollow()
         */
        $group->post('/questions/{question_id:\d+}/followers', 'QuestionApi/addFollow')
            ->add(NeedLogin::class);

        /**
         * 取消关注指定提问
         *
         * @see QuestionApi::deleteFollow()
         */
        $group->delete('/questions/{question_id:\d+}/followers', 'QuestionApi/deleteFollow')
            ->add(NeedLogin::class);

        /**
         * 获取指定提问下的评论
         *
         * @see QuestionApi::getComments()
         */
        $group->get('/questions/{question_id:\d+}/comments', 'QuestionApi/getComments')
            ->add(TransformComment::class);

        /**
         * 在指定提问下发表评论
         *
         * @see QuestionApi::createComment()
         */
        $group->post('/questions/{question_id:\d+}/comments', 'QuestionApi/createComment')
            ->add(NeedLogin::class)
            ->add(TransformComment::class);

        /**
         * 获取指定提问下的回答
         *
         * @see QuestionApi::getAnswers()
         */
        $group->get('/questions/{question_id:\d+}/answers', 'QuestionApi/getAnswers')
            ->add(TransformAnswer::class);

        /**
         * 在指定提问下发表回答
         *
         * @see QuestionApi::createAnswer()
         */
        $group->post('/questions/{question_id:\d+}/answers', 'QuestionApi/createAnswer')
            ->add(NeedLogin::class)
            ->add(TransformAnswer::class);

        /**
         * 获取已删除的提问
         *
         * @see QuestionApi::getDeleted()
         */
        $group->get('/trash/questions', 'QuestionApi/getDeleted')
            ->add(NeedManager::class);

        /**
         * 批量恢复提问
         *
         * @see QuestionApi::restoreMultiple()
         */
        $group->post('/trash/questions', 'QuestionApi/restoreMultiple')
            ->add(NeedManager::class);

        /**
         * 批量销毁提问
         *
         * @see QuestionApi::destroyMultiple()
         */
        $group->delete('/trash/questions', 'QuestionApi/destroyMultiple')
            ->add(NeedManager::class);

        /**
         * 启用提问
         *
         * @see QuestionApi::restore()
         */
        $group->post('/trash/questions/{question_id:\d+}', 'QuestionApi/restore')
            ->add(NeedManager::class);

        /**
         * 销毁提问
         *
         * @see QuestionApi::destroy()
         */
        $group->delete('/trash/questions/{question_id:\d+}', 'QuestionApi/destroy')
            ->add(NeedManager::class);
    }

    /**
     * 回答
     *
     * @param RouteCollectorProxy $group
     */
    public function answerApi(RouteCollectorProxy $group): void
    {
        /**
         * 获取回答列表
         *
         * @see AnswerApi::getList()
         */
        $group->get('/answers', 'AnswerApi/getList')
            ->add(TransformAnswer::class);

        /**
         * 批量删除回答
         *
         * @see AnswerApi::deleteMultiple()
         */
        $group->delete('/answers', 'AnswerApi/deleteMultiple');

        /**
         * 获取回答
         *
         * @see AnswerApi::get()
         */
        $group->get('/answers/{answer_id:\d+}', 'AnswerApi/get')
            ->add(TransformAnswer::class);

        /**
         * 更新回答
         *
         * @see AnswerApi::update()
         */
        $group->patch('/answers/{answer_id:\d+}', 'AnswerApi/update');

        /**
         * 删除回答
         *
         * @see AnswerApi::delete()
         */
        $group->delete('/answers/{answer_id:\d+}', 'AnswerApi/delete');

        /**
         * 获取指定回答的投票者
         *
         * @see AnswerApi::getVoters()
         */
        $group->get('/answers/{answer_id:\d+}/voters', 'AnswerApi/getVoters')
            ->add(TransformUser::class);

        /**
         * 为指定回答添加投票
         *
         * @see AnswerApi::addVote()
         */
        $group->post('/answers/{answer_id:\d+}/voters', 'AnswerApi/addVote')
            ->add(NeedLogin::class);

        /**
         * 取消对指定回答的投票
         *
         * @see AnswerApi::deleteVote()
         */
        $group->delete('/answers/{answer_id:\d+}/voters', 'AnswerApi/deleteVote')
            ->add(NeedLogin::class);

        /**
         * 获取指定回答下的评论
         *
         * @see AnswerApi::getComments()
         */
        $group->get('/answers/{answer_id:\d+}/comments', 'AnswerApi/getComments')
            ->add(TransformComment::class);

        /**
         * 在指定回答下发表评论
         *
         * @see AnswerApi::createComment()
         */
        $group->post('/answers/{answer_id:\d+}/comments', 'AnswerApi/createComment')
            ->add(NeedLogin::class)
            ->add(TransformComment::class);

        /**
         * 获取已删除的回答
         *
         * @see AnswerApi::getDeleted()
         */
        $group->get('/trash/answers', 'AnswerApi/getDeleted')
            ->add(NeedManager::class);

        /**
         * 批量恢复回答
         *
         * @see AnswerApi::restoreMultiple()
         */
        $group->post('/trash/answers', 'AnswerApi/restoreMultiple')
            ->add(NeedManager::class);

        /**
         * 批量销毁回答
         *
         * @see AnswerApi::destroyMultiple()
         */
        $group->delete('/trash/answers', 'AnswerApi/destroyMultiple')
            ->add(NeedManager::class);

        /**
         * 恢复回答
         *
         * @see AnswerApi::restore()
         */
        $group->post('/trash/answers/{answer_id:\d+}', 'AnswerApi/restore')
            ->add(NeedManager::class);

        /**
         * 销毁回答
         *
         * @see AnswerApi::destroy()
         */
        $group->delete('/trash/answers/{answer_id:\d+}', 'AnswerApi/destroy')
            ->add(NeedManager::class);
    }

    /**
     * 文章
     *
     * @param RouteCollectorProxy $group
     */
    public function articleApi(RouteCollectorProxy $group): void
    {
        /**
         * 获取文章列表
         *
         * @see ArticleApi::getList()
         */
        $group->get('/articles', 'ArticleApi/getList')
            ->add(TransformArticle::class);

        /**
         * 发表文章
         *
         * @see ArticleApi::create()
         */
        $group->post('/articles', 'ArticleApi/create');

        /**
         * 批量删除文章
         *
         * @see ArticleApi::deleteMultiple()
         */
        $group->delete('/articles', 'ArticleApi/deleteMultiple');

        /**
         * 获取文章
         *
         * @see ArticleApi::get()
         */
        $group->get('/articles/{article_id:\d+}', 'ArticleApi/get');

        /**
         * 更新文章
         *
         * @see ArticleApi::update()
         */
        $group->patch('/articles/{article_id:\d+}', 'ArticleApi/update');

        /**
         * 删除文章
         *
         * @see ArticleApi::delete()
         */
        $group->delete('/articles/{article_id:\d+}', 'ArticleApi/delete');

        /**
         * 获取指定文章的投票者
         *
         * @see ArticleApi::getVoters()
         */
        $group->get('/articles/{article_id:\d+}/voters', 'ArticleApi/getVoters')
            ->add(TransformUser::class);

        /**
         * 为指定文章投票
         *
         * @see ArticleApi::addVote()
         */
        $group->post('/articles/{article_id:\d+}/voters', 'ArticleApi/addVote')
            ->add(NeedLogin::class);

        /**
         * 取消对指定文章的投票
         *
         * @see ArticleApi::deleteVote()
         */
        $group->delete('/articles/{article_id:\d+}/voters', 'ArticleApi/deleteVote')
            ->add(NeedLogin::class);

        /**
         * 获取指定文章的关注者
         *
         * @see ArticleApi::getFollowers()
         */
        $group->get('/articles/{article_id:\d+}/followers', 'ArticleApi/getFollowers')
            ->add(TransformUser::class);

        /**
         * 关注指定文章
         *
         * @see ArticleApi::addFollow()
         */
        $group->post('/articles/{article_id:\d+}/followers', 'ArticleApi/addFollow')
            ->add(NeedLogin::class);

        /**
         * 取消关注指定文章
         *
         * @see ArticleApi::deleteFollow()
         */
        $group->delete('/articles/{article_id:\d+}/followers', 'ArticleApi/deleteFollow')
            ->add(NeedLogin::class);

        /**
         * 获取指定文章下的评论
         *
         * @see ArticleApi::getComments()
         */
        $group->get('/articles/{article_id:\d+}/comments', 'ArticleApi/getComments')
            ->add(TransformComment::class);

        /**
         * 在指定文章下发表评论
         *
         * @see ArticleApi::createComment()
         */
        $group->post('/articles/{article_id:\d+}/comments', 'ArticleApi/createComment')
            ->add(NeedLogin::class)
            ->add(TransformComment::class);

        /**
         * 获取已删除的文章
         *
         * @see ArticleApi::getDeleted()
         */
        $group->get('/trash/articles', 'ArticleApi/getDeleted')
            ->add(NeedManager::class);

        /**
         * 批量恢复文章
         *
         * @see ArticleApi::restoreMultiple()
         */
        $group->post('/trash/articles', 'ArticleApi/restoreMultiple')
            ->add(NeedManager::class);

        /**
         * 批量销毁文章
         *
         * @see ArticleApi::destroyMultiple()
         */
        $group->delete('/trash/articles', 'ArticleApi/destroyMultiple')
            ->add(NeedManager::class);

        /**
         * 恢复文章
         *
         * @see ArticleApi::restore()
         */
        $group->post('/trash/articles/{article_id:\d+}', 'ArticleApi/restore')
            ->add(NeedManager::class);

        /**
         * 销毁文章
         *
         * @see ArticleApi::destroy()
         */
        $group->delete('/trash/articles/{article_id:\d+}', 'ArticleApi/destroy')
            ->add(NeedManager::class);
    }

    /**
     * 评论
     *
     * @param RouteCollectorProxy $group
     */
    public function commentApi(RouteCollectorProxy $group): void
    {
        /**
         * 获取评论列表
         *
         * @see CommentApi::getList()
         */
        $group->get('/comments', 'CommentApi/getList')
            ->add(TransformComment::class);

        /**
         * 批量删除评论
         *
         * @see CommentApi::deleteMultiple()
         */
        $group->delete('/comments', 'CommentApi/deleteMultiple');

        /**
         * 获取评论
         *
         * @see CommentApi::get()
         */
        $group->get('/comments/{comment_id:\d+}', 'CommentApi/get');

        /**
         * 更新评论
         *
         * @see CommentApi::update()
         */
        $group->patch('/comments/{comment_id:\d+}', 'CommentApi/update');

        /**
         * 删除评论
         *
         * @see CommentApi::delete()
         */
        $group->delete('/comments/{comment_id:\d+}', 'CommentApi/delete');

        /**
         * 获取指定评论的投票者
         *
         * @see CommentApi::getVoters()
         */
        $group->get('/comments/{comment_id:\d+}/voters', 'CommentApi/getVoters')
            ->add(TransformUser::class);

        /**
         * 为指定评论添加投票
         *
         * @see CommentApi::addVote()
         */
        $group->post('/comments/{comment_id:\d+}/voters', 'CommentApi/addVote')
            ->add(NeedLogin::class);

        /**
         * 取消对指定评论的投票
         *
         * @see CommentApi::deleteVote()
         */
        $group->delete('/comments/{comment_id:\d+}/voters', 'CommentApi/deleteVote')
            ->add(NeedLogin::class);

        /**
         * 获取已删除的评论
         *
         * @see CommentApi::getDeleted()
         */
        $group->get('/trash/comments', 'CommentApi/getDeleted')
            ->add(NeedManager::class);

        /**
         * 批量恢复评论
         *
         * @see CommentApi::restoreMultiple()
         */
        $group->post('/trash/comments', 'CommentApi/restoreMultiple')
            ->add(NeedManager::class);

        /**
         * 批量销毁评论
         *
         * @see CommentApi::destroyMultiple()
         */
        $group->delete('/trash/comments', 'CommentApi/destroyMultiple')
            ->add(NeedManager::class);

        /**
         * 恢复评论
         *
         * @see CommentApi::restore()
         */
        $group->post('/trash/comments/{comment_id:\d+}', 'CommentApi/restore')
            ->add(NeedManager::class);

        /**
         * 销毁评论
         *
         * @see CommentApi::destroy()
         */
        $group->delete('/trash/comments/{comment_id:\d+}', 'CommentApi/destroy')
            ->add(NeedManager::class);
    }

    /**
     * 举报
     *
     * @param RouteCollectorProxy $group
     */
    public function reportApi(RouteCollectorProxy $group): void
    {
        /**
         * 获取举报列表
         *
         * @see ReportApi::getList()
         */
        $group->get('/reports', 'ReportApi/getList')
            ->add(NeedManager::class);

        /**
         * 批量删除举报
         *
         * @see ReportApi::deleteMultiple()
         */
        $group->delete('/reports', 'ReportApi/deleteMultiple')
            ->add(NeedManager::class);

        /**
         * 获取举报理由列表
         *
         * @see ReportApi::getReasons()
         */
        $group->get('/reports/{reportable_type}/{reportable_id:\d+}', 'ReportApi/getReasons')
            ->add(NeedManager::class);

        /**
         * 添加举报
         *
         * @see ReportApi::create()
         */
        $group->post('/reports/{reportable_type}/{reportable_id:\d+}', 'ReportApi/create');

        /**
         * 删除举报
         *
         * @see ReportApi::delete()
         */
        $group->delete('/reports/{reportable_type}/{reportable_id:\d+}', 'ReportApi/delete')
            ->add(NeedManager::class);
    }

    /**
     * 私信
     *
     * @param RouteCollectorProxy $group
     */
    public function inboxApi(RouteCollectorProxy $group): void
    {

    }

    /**
     * 通知
     *
     * @param RouteCollectorProxy $group
     */
    public function notificationApi(RouteCollectorProxy $group): void
    {

    }

    /**
     * 图形验证码
     *
     * @param RouteCollectorProxy $group
     */
    public function captchaApi(RouteCollectorProxy $group): void
    {
        /**
         * 创建图形验证码
         *
         * @see CaptchaApi::create()
         */
        $group->post('/captchas', 'CaptchaApi/create');
    }

    /**
     * 邮件
     *
     * @param RouteCollectorProxy $group
     */
    public function emailApi(RouteCollectorProxy $group): void
    {
        /**
         * 发送邮件
         *
         * @see EmailApi::send()
         */
        $group->post('/emails', 'EmailApi/send')
            ->add(NeedManager::class);
    }

    /**
     * 图片
     *
     * @param RouteCollectorProxy $group
     */
    public function imageApi(RouteCollectorProxy $group): void
    {
        /**
         * 获取图片列表
         *
         * @see ImageApi::getList()
         */
        $group->get('/images', 'ImageApi/getList')
            ->add(NeedManager::class)
            ->add(TransformImage::class);

        /**
         * 上传图片
         *
         * @see ImageApi::upload()
         */
        $group->post('/images', 'ImageApi/upload')
            ->add(NeedLogin::class)
            ->add(TransformImage::class);

        /**
         * 批量删除图片
         *
         * @see ImageApi::deleteMultiple()
         */
        $group->delete('/images', 'ImageApi/deleteMultiple')
            ->add(NeedManager::class);

        /**
         * 获取图片
         *
         * @see ImageApi::get()
         */
        $group->get('/images/{key}', 'ImageApi/get')
            ->add(TransformImage::class);

        /**
         * 更新图片
         *
         * @see ImageApi::update()
         */
        $group->patch('/images/{key}', 'ImageApi/update')
            ->add(NeedManager::class)
            ->add(TransformImage::class);

        /**
         * 删除图片
         *
         * @see ImageApi::delete()
         */
        $group->delete('/images/{key}', 'ImageApi/delete')
            ->add(NeedManager::class);
    }
}
