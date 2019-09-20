<?php

declare(strict_types=1);

namespace MDClub\Route;

use MDClub\Controller\RestApi\Answer;
use MDClub\Controller\RestApi\Article;
use MDClub\Controller\RestApi\Captcha;
use MDClub\Controller\RestApi\Comment;
use MDClub\Controller\RestApi\Email;
use MDClub\Controller\RestApi\Image;
use MDClub\Controller\RestApi\Option;
use MDClub\Controller\RestApi\Question;
use MDClub\Controller\RestApi\Report;
use MDClub\Controller\RestApi\Token;
use MDClub\Controller\RestApi\Topic;
use MDClub\Controller\RestApi\User;
use MDClub\Middleware\EnableCrossRequest;
use MDClub\Middleware\JsonBodyParser;
use MDClub\Middleware\NeedLogin;
use MDClub\Middleware\NeedManager;
use MDClub\Middleware\Transform\Answer as TransformAnswer;
use MDClub\Middleware\Transform\Article as TransformArticle;
use MDClub\Middleware\Transform\Comment as TransformComment;
use MDClub\Middleware\Transform\Image as TransformImage;
use MDClub\Middleware\Transform\Question as TransformQuestion;
use MDClub\Middleware\Transform\Report as TransformReport;
use MDClub\Middleware\Transform\ReportReason as TransformReportReason;
use MDClub\Middleware\Transform\Topic as TransformTopic;
use MDClub\Middleware\Transform\User as TransformUser;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

/**
 * RestApi 路由
 */
class RestApi
{
    public function __construct(App $app)
    {
        $route = $this;

        $app
            ->group('/api', function (RouteCollectorProxy $group) use ($route) {
                $route
                    ->token($group)
                    ->option($group)
                    ->user($group)
                    ->topic($group)
                    ->question($group)
                    ->answer($group)
                    ->article($group)
                    ->comment($group)
                    ->report($group)
                    ->inbox($group)
                    ->notification($group)
                    ->captcha($group)
                    ->email($group)
                    ->image($group);
            })
            ->add(EnableCrossRequest::class)
            ->add(JsonBodyParser::class);
    }

    /**
     * 身份验证
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function token(RouteCollectorProxy $group): self
    {
        /**
         * 生成 Token
         *
         * @see Token::create()
         */
        $group->post('/tokens', Token::class . ':create');

        return $this;
    }

    /**
     * 系统设置
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function option(RouteCollectorProxy $group): self
    {
        /**
         * 获取设置
         *
         * @see Option::get()
         */
        $group->get('/options', Option::class . ':get');

        /**
         * 更新设置
         *
         * @see Option::update()
         */
        $group->patch('/options', Option::class . ':update')
            ->add(NeedManager::class);

        return $this;
    }

    /**
     * 用户
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function user(RouteCollectorProxy $group): self
    {
        /**
         * 获取用户列表
         *
         * @see User::getList()
         */
        $group->get('/users', User::class . ':getList')
            ->add(TransformUser::class);

        /**
         * 注册账户
         *
         * @see User::register()
         */
        $group->post('/users', User::class . ':register');

        /**
         * 批量禁用用户
         *
         * @see User::disableMultiple()
         */
        $group->delete('/users', User::class . ':disableMultiple')
            ->add(NeedManager::class);

        /**
         * 获取指定用户信息
         *
         * @see User::get()
         */
        $group->get('/users/{user_id:\d+}', User::class . ':get')
            ->add(TransformUser::class);

        /**
         * 更新指定用户信息
         *
         * @see User::update()
         */
        $group->patch('/users/{user_id:\d+}', User::class . ':update')
            ->add(NeedManager::class);

        /**
         * 禁用指定用户
         *
         * @see User::disable()
         */
        $group->delete('/users/{user_id:\d+}', User::class . ':disable')
            ->add(NeedManager::class);

        /**
         * 删除指定用户的头像
         *
         * @see User::deleteAvatar()
         */
        $group->delete('/users/{user_id:\d+}/avatar', User::class . ':deleteAvatar')
            ->add(NeedManager::class);

        /**
         * 删除指定用户的封面
         *
         * @see User::deleteCover()
         */
        $group->delete('/users/{user_id:\d+}/cover', User::class . ':deleteCover')
            ->add(NeedManager::class);

        /**
         * 获取指定用户的关注者
         *
         * @see User::getFollowers()
         */
        $group->get('/users/{user_id:\d+}/followers', User::class . ':getFollowers')
            ->add(TransformUser::class);

        /**
         * 关注指定用户
         *
         * @see User::addFollow()
         */
        $group->post('/users/{user_id:\d+}/followers', User::class . ':addFollow')
            ->add(NeedLogin::class);

        /**
         * 取消关注指定用户
         *
         * @see User::deleteFollow()
         */
        $group->delete('/users/{user_id:\d+}/followers', User::class . ':deleteFollow')
            ->add(NeedLogin::class);

        /**
         * 获取指定用户关注的用户
         *
         * @see User::getFollowees()
         */
        $group->get('/users/{user_id:\d+}/followees', User::class . ':getFollowees')
            ->add(TransformUser::class);

        /**
         * 获取指定用户关注的提问
         *
         * @see User::getFollowingQuestions()
         */
        $group->get('/users/{user_id:\d+}/following_questions', User::class . ':getFollowingQuestions')
            ->add(TransformQuestion::class);

        /**
         * 获取指定用户关注的文章
         *
         * @see User::getFollowingArticles()
         */
        $group->get('/users/{user_id:\d+}/following_articles', User::class . ':getFollowingArticles')
            ->add(TransformArticle::class);

        /**
         * 获取指定用户关注的话题
         *
         * @see User::getFollowingTopics()
         */
        $group->get('/users/{user_id:\d+}/following_topics', User::class . ':getFollowingTopics')
            ->add(TransformTopic::class);

        /**
         * 获取指定用户发表的提问
         *
         * @see User::getQuestions()
         */
        $group->get('/users/{user_id:\d+}/questions', User::class . ':getQuestions')
            ->add(TransformQuestion::class);

        /**
         * 获取指定用户发表的回答
         *
         * @see User::getAnswers()
         */
        $group->get('/users/{user_id:\d+}/answers', User::class . ':getAnswers')
            ->add(TransformAnswer::class);

        /**
         * 获取指定用户发表的文章
         *
         * @see User::getArticles()
         */
        $group->get('/users/{user_id:\d+}/articles', User::class . ':getArticles')
            ->add(TransformArticle::class);

        /**
         * 获取指定用户发表的评论
         *
         * @see User::getComments()
         */
        $group->get('/users/{user_id:\d+}/comments', User::class . ':getComments')
            ->add(TransformComment::class);

        /**
         * 获取当前用户信息
         *
         * @see User::getMine()
         */
        $group->get('/user', User::class . ':getMine')
            ->add(NeedLogin::class)
            ->add(TransformUser::class);

        /**
         * 更新当前用户信息
         *
         * @see User::updateMine()
         */
        $group->patch('/user', User::class . ':updateMine')
            ->add(NeedLogin::class)
            ->add(TransformUser::class);

        /**
         * 上传当前用户头像
         *
         * @see User::uploadMyAvatar()
         */
        $group->post('/user/avatar', User::class . ':uploadMyAvatar')
            ->add(NeedLogin::class);

        /**
         * 删除当前用户头像
         *
         * @see User::deleteMyAvatar()
         */
        $group->delete('/user/avatar', User::class . ':deleteMyAvatar')
            ->add(NeedLogin::class);

        /**
         * 上传当前用户封面
         *
         * @see User::uploadMyCover()
         */
        $group->post('/user/cover', User::class . ':uploadMyCover')
            ->add(NeedLogin::class);

        /**
         * 删除当前用户封面
         *
         * @see User::deleteMyCover()
         */
        $group->delete('/user/cover', User::class . ':deleteMyCover')
            ->add(NeedLogin::class);

        /**
         * 发送注册验证邮件
         *
         * @see User::sendRegisterEmail()
         */
        $group->post('/user/register/email', User::class . ':sendRegisterEmail');

        /**
         * 发送重置密码验证邮件
         *
         * @see User::sendPasswordResetEmail()
         */
        $group->post('/user/password/email', User::class . ':sendPasswordResetEmail');

        /**
         * 重置密码
         *
         * @see User::updatePassword()
         */
        $group->put('/user/password', User::class . ':updatePassword');

        /**
         * 获取当前用户的关注者
         *
         * @see User::getMyFollowers()
         */
        $group->get('/user/followers', User::class . ':getMyFollowers')
            ->add(NeedLogin::class)
            ->add(TransformUser::class);

        /**
         * 获取当前用户关注的用户
         *
         * @see User::getMyFollowees()
         */
        $group->get('/user/followees', User::class . ':getMyFollowees')
            ->add(NeedLogin::class)
            ->add(TransformUser::class);

        /**
         * 获取当前用户关注的提问
         *
         * @see User::getMyFollowingQuestions()
         */
        $group->get('/user/following_questions', User::class . ':getMyFollowingQuestions')
            ->add(NeedLogin::class)
            ->add(TransformQuestion::class);

        /**
         * 获取当前用户关注的文章
         *
         * @see User::getMyFollowingArticles()
         */
        $group->get('/user/following_articles', User::class . ':getMyFollowingArticles')
            ->add(NeedLogin::class)
            ->add(TransformArticle::class);

        /**
         * 获取当前用户关注的话题
         *
         * @see User::getMyFollowingTopics()
         */
        $group->get('/user/following_topics', User::class . ':getMyFollowingTopics')
            ->add(NeedLogin::class)
            ->add(TransformTopic::class);

        /**
         * 获取当前用户发表的提问
         *
         * @see User::getMyQuestions()
         */
        $group->get('/user/questions', User::class . ':getMyQuestions')
            ->add(NeedLogin::class)
            ->add(TransformQuestion::class);

        /**
         * 获取当前用户发表的回答
         *
         * @see User::getMyAnswers()
         */
        $group->get('/user/answers', User::class . ':getMyAnswers')
            ->add(NeedLogin::class)
            ->add(TransformAnswer::class);

        /**
         * 获取当前用户发表的文章
         *
         * @see User::getMyArticles()
         */
        $group->get('/user/articles', User::class . ':getMyArticles')
            ->add(NeedLogin::class)
            ->add(TransformArticle::class);

        /**
         * 获取当前用户发表的评论
         *
         * @see User::getMyComments()
         */
        $group->get('/user/comments', User::class . ':getMyComments')
            ->add(NeedLogin::class)
            ->add(TransformComment::class);

        /**
         * 获取已禁用的用户列表
         *
         * @see User::getDisabled()
         */
        $group->get('/trash/users', User::class . ':getDisabled')
            ->add(NeedManager::class)
            ->add(TransformUser::class);

        /**
         * 批量启用用户
         *
         * @see User::enableMultiple()
         */
        $group->post('/trash/users', User::class . ':enableMultiple')
            ->add(NeedManager::class);

        /**
         * 启用用户
         *
         * @see User::enable()
         */
        $group->post('/trash/users/{user_id:\d+}', User::class . ':enable')
            ->add(NeedManager::class);

        return $this;
    }

    /**
     * 话题
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function topic(RouteCollectorProxy $group): self
    {
        /**
         * 获取话题列表
         *
         * @see Topic::getList()
         */
        $group->get('/topics', Topic::class . ':getList')
            ->add(TransformTopic::class);

        /**
         * 发表话题
         *
         * @see Topic::create()
         */
        $group->post('/topics', Topic::class . ':create')
            ->add(NeedManager::class)
            ->add(TransformTopic::class);

        /**
         * 批量删除话题
         *
         * @see Topic::deleteMultiple()
         */
        $group->delete('/topics', Topic::class . ':deleteMultiple')
            ->add(NeedManager::class);

        /**
         * 获取指定话题
         *
         * @see Topic::get()
         */
        $group->get('/topics/{topic_id:\d+}', Topic::class . ':get')
            ->add(TransformTopic::class);

        /**
         * 更新话题
         *
         * NOTE: formData 数据只能通过 post 请求提交，所以这里不用 patch 请求
         *
         * @see Topic::update()
         */
        $group->post('/topics/{topic_id:\d+}', Topic::class . ':update')
            ->add(NeedManager::class)
            ->add(TransformTopic::class);

        /**
         * 删除话题
         *
         * @see Topic::delete()
         */
        $group->delete('/topics/{topic_id:\d+}', Topic::class . ':delete')
            ->add(NeedManager::class);

        /**
         * 获取指定话题的关注者
         *
         * @see Topic::getFollowers()
         */
        $group->get('/topics/{topic_id:\d+}/followers', Topic::class . ':getFollowers')
            ->add(TransformUser::class);

        /**
         * 关注指定话题
         *
         * @see Topic::addFollow()
         */
        $group->post('/topics/{topic_id:\d+}/followers', Topic::class . ':addFollow')
            ->add(NeedLogin::class);

        /**
         * 取消关注指定话题
         *
         * @see Topic::deleteFollow()
         */
        $group->delete('/topics/{topic_id:\d+}/followers', Topic::class . ':deleteFollow')
            ->add(NeedLogin::class);

        /**
         * 获取指定话题下的提问
         *
         * @see Topic::getQuestions()
         */
        $group->get('/topics/{topic_id:\d+}/questions', Topic::class . ':getQuestions')
            ->add(TransformQuestion::class);

        /**
         * 获取指定话题下的文章
         *
         * @see Topic::getArticles()
         */
        $group->get('/topics/{topic_id:\d+}/articles', Topic::class . ':getArticles')
            ->add(TransformArticle::class);

        /**
         * 获取回收站中的话题
         *
         * @see Topic::getDeleted()
         */
        $group->get('/trash/topics', Topic::class . ':getDeleted')
            ->add(NeedManager::class)
            ->add(TransformTopic::class);

        /**
         * 批量恢复话题
         *
         * @see Topic::restoreMultiple()
         */
        $group->post('/trash/topics', Topic::class . ':restoreMultiple')
            ->add(NeedManager::class);

        /**
         * 批量销毁话题
         *
         * @see Topic::destroyMultiple()
         */
        $group->delete('/trash/topics', Topic::class . ':destroyMultiple')
            ->add(NeedManager::class);

        /**
         * 恢复话题
         *
         * @see Topic::restore()
         */
        $group->post('/trash/topics/{topic_id:\d+}', Topic::class . ':restore')
            ->add(NeedManager::class)
            ->add(TransformTopic::class);

        /**
         * 销毁话题
         *
         * @see Topic::destroy()
         */
        $group->delete('/trash/topics/{topic_id:\d+}', Topic::class . ':destroy')
            ->add(NeedManager::class);

        return $this;
    }

    /**
     * 提问
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function question(RouteCollectorProxy $group): self
    {
        /**
         * 获取提问列表
         *
         * @see Question::getList()
         */
        $group->get('/questions', Question::class . ':getList')
            ->add(TransformQuestion::class);

        /**
         * 发表提问
         *
         * @see Question::create()
         */
        $group->post('/questions', Question::class . ':create');

        /**
         * 批量删除提问
         *
         * @see Question::deleteMultiple()
         */
        $group->delete('/questions', Question::class . ':deleteMultiple')
            ->add(NeedLogin::class);

        /**
         * 获取提问
         *
         * @see Question::get()
         */
        $group->get('/questions/{question_id:\d+}', Question::class . ':get')
            ->add(TransformQuestion::class);

        /**
         * 更新提问
         *
         * @see Question::update()
         */
        $group->patch('/questions/{question_id:\d+}', Question::class . ':update');

        /**
         * 删除提问
         *
         * @see Question::delete()
         */
        $group->delete('/questions/{question_id:\d+}', Question::class . ':delete')
            ->add(NeedLogin::class);

        /**
         * 获取指定提问的投票者
         *
         * @see Question::getVoters()
         */
        $group->get('/questions/{question_id:\d+}/voters', Question::class . ':getVoters')
            ->add(TransformUser::class);

        /**
         * 给指定提问投票
         *
         * @see Question::addVote()
         */
        $group->post('/questions/{question_id:\d+}/voters', Question::class . ':addVote')
            ->add(NeedLogin::class);

        /**
         * 取消给指定提问的投票
         *
         * @see Question::deleteVote()
         */
        $group->delete('/questions/{question_id:\d+}/voters', Question::class . ':deleteVote')
            ->add(NeedLogin::class);

        /**
         * 获取指定提问的关注者
         *
         * @see Question::getFollowers()
         */
        $group->get('/questions/{question_id:\d+}/followers', Question::class . ':getFollowers')
            ->add(TransformUser::class);

        /**
         * 关注指定提问
         *
         * @see Question::addFollow()
         */
        $group->post('/questions/{question_id:\d+}/followers', Question::class . ':addFollow')
            ->add(NeedLogin::class);

        /**
         * 取消关注指定提问
         *
         * @see Question::deleteFollow()
         */
        $group->delete('/questions/{question_id:\d+}/followers', Question::class . ':deleteFollow')
            ->add(NeedLogin::class);

        /**
         * 获取指定提问下的评论
         *
         * @see Question::getComments()
         */
        $group->get('/questions/{question_id:\d+}/comments', Question::class . ':getComments')
            ->add(TransformComment::class);

        /**
         * 在指定提问下发表评论
         *
         * @see Question::createComment()
         */
        $group->post('/questions/{question_id:\d+}/comments', Question::class . ':createComment')
            ->add(NeedLogin::class)
            ->add(TransformComment::class);

        /**
         * 获取指定提问下的回答
         *
         * @see Question::getAnswers()
         */
        $group->get('/questions/{question_id:\d+}/answers', Question::class . ':getAnswers')
            ->add(TransformAnswer::class);

        /**
         * 在指定提问下发表回答
         *
         * @see Question::createAnswer()
         */
        $group->post('/questions/{question_id:\d+}/answers', Question::class . ':createAnswer')
            ->add(NeedLogin::class)
            ->add(TransformAnswer::class);

        /**
         * 获取已删除的提问
         *
         * @see Question::getDeleted()
         */
        $group->get('/trash/questions', Question::class . ':getDeleted')
            ->add(NeedManager::class);

        /**
         * 批量恢复提问
         *
         * @see Question::restoreMultiple()
         */
        $group->post('/trash/questions', Question::class . ':restoreMultiple')
            ->add(NeedManager::class);

        /**
         * 批量销毁提问
         *
         * @see Question::destroyMultiple()
         */
        $group->delete('/trash/questions', Question::class . ':destroyMultiple')
            ->add(NeedManager::class);

        /**
         * 启用提问
         *
         * @see Question::restore()
         */
        $group->post('/trash/questions/{question_id:\d+}', Question::class . ':restore')
            ->add(NeedManager::class);

        /**
         * 销毁提问
         *
         * @see Question::destroy()
         */
        $group->delete('/trash/questions/{question_id:\d+}', Question::class . ':destroy')
            ->add(NeedManager::class);

        return $this;
    }

    /**
     * 回答
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function answer(RouteCollectorProxy $group): self
    {
        /**
         * 获取回答列表
         *
         * @see Answer::getList()
         */
        $group->get('/answers', Answer::class . ':getList')
            ->add(TransformAnswer::class);

        /**
         * 批量删除回答
         *
         * @see Answer::deleteMultiple()
         */
        $group->delete('/answers', Answer::class . ':deleteMultiple');

        /**
         * 获取回答
         *
         * @see Answer::get()
         */
        $group->get('/answers/{answer_id:\d+}', Answer::class . ':get')
            ->add(TransformAnswer::class);

        /**
         * 更新回答
         *
         * @see Answer::update()
         */
        $group->patch('/answers/{answer_id:\d+}', Answer::class . ':update');

        /**
         * 删除回答
         *
         * @see Answer::delete()
         */
        $group->delete('/answers/{answer_id:\d+}', Answer::class . ':delete');

        /**
         * 获取指定回答的投票者
         *
         * @see Answer::getVoters()
         */
        $group->get('/answers/{answer_id:\d+}/voters', Answer::class . ':getVoters')
            ->add(TransformUser::class);

        /**
         * 为指定回答添加投票
         *
         * @see Answer::addVote()
         */
        $group->post('/answers/{answer_id:\d+}/voters', Answer::class . ':addVote')
            ->add(NeedLogin::class);

        /**
         * 取消对指定回答的投票
         *
         * @see Answer::deleteVote()
         */
        $group->delete('/answers/{answer_id:\d+}/voters', Answer::class . ':deleteVote')
            ->add(NeedLogin::class);

        /**
         * 获取指定回答下的评论
         *
         * @see Answer::getComments()
         */
        $group->get('/answers/{answer_id:\d+}/comments', Answer::class . ':getComments')
            ->add(TransformComment::class);

        /**
         * 在指定回答下发表评论
         *
         * @see Answer::createComment()
         */
        $group->post('/answers/{answer_id:\d+}/comments', Answer::class . ':createComment')
            ->add(NeedLogin::class)
            ->add(TransformComment::class);

        /**
         * 获取已删除的回答
         *
         * @see Answer::getDeleted()
         */
        $group->get('/trash/answers', Answer::class . ':getDeleted')
            ->add(NeedManager::class);

        /**
         * 批量恢复回答
         *
         * @see Answer::restoreMultiple()
         */
        $group->post('/trash/answers', Answer::class . ':restoreMultiple')
            ->add(NeedManager::class);

        /**
         * 批量销毁回答
         *
         * @see Answer::destroyMultiple()
         */
        $group->delete('/trash/answers', Answer::class . ':destroyMultiple')
            ->add(NeedManager::class);

        /**
         * 恢复回答
         *
         * @see Answer::restore()
         */
        $group->post('/trash/answers/{answer_id:\d+}', Answer::class . ':restore')
            ->add(NeedManager::class);

        /**
         * 销毁回答
         *
         * @see Answer::destroy()
         */
        $group->delete('/trash/answers/{answer_id:\d+}', Answer::class . ':destroy')
            ->add(NeedManager::class);

        return $this;
    }

    /**
     * 文章
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function article(RouteCollectorProxy $group): self
    {
        /**
         * 获取文章列表
         *
         * @see Article::getList()
         */
        $group->get('/articles', Article::class . ':getList')
            ->add(TransformArticle::class);

        /**
         * 发表文章
         *
         * @see Article::create()
         */
        $group->post('/articles', Article::class . ':create');

        /**
         * 批量删除文章
         *
         * @see Article::deleteMultiple()
         */
        $group->delete('/articles', Article::class . ':deleteMultiple');

        /**
         * 获取文章
         *
         * @see Article::get()
         */
        $group->get('/articles/{article_id:\d+}', Article::class . ':get');

        /**
         * 更新文章
         *
         * @see Article::update()
         */
        $group->patch('/articles/{article_id:\d+}', Article::class . ':update');

        /**
         * 删除文章
         *
         * @see Article::delete()
         */
        $group->delete('/articles/{article_id:\d+}', Article::class . ':delete');

        /**
         * 获取指定文章的投票者
         *
         * @see Article::getVoters()
         */
        $group->get('/articles/{article_id:\d+}/voters', Article::class . ':getVoters')
            ->add(TransformUser::class);

        /**
         * 为指定文章投票
         *
         * @see Article::addVote()
         */
        $group->post('/articles/{article_id:\d+}/voters', Article::class . ':addVote')
            ->add(NeedLogin::class);

        /**
         * 取消对指定文章的投票
         *
         * @see Article::deleteVote()
         */
        $group->delete('/articles/{article_id:\d+}/voters', Article::class . ':deleteVote')
            ->add(NeedLogin::class);

        /**
         * 获取指定文章的关注者
         *
         * @see Article::getFollowers()
         */
        $group->get('/articles/{article_id:\d+}/followers', Article::class . ':getFollowers')
            ->add(TransformUser::class);

        /**
         * 关注指定文章
         *
         * @see Article::addFollow()
         */
        $group->post('/articles/{article_id:\d+}/followers', Article::class . ':addFollow')
            ->add(NeedLogin::class);

        /**
         * 取消关注指定文章
         *
         * @see Article::deleteFollow()
         */
        $group->delete('/articles/{article_id:\d+}/followers', Article::class . ':deleteFollow')
            ->add(NeedLogin::class);

        /**
         * 获取指定文章下的评论
         *
         * @see Article::getComments()
         */
        $group->get('/articles/{article_id:\d+}/comments', Article::class . ':getComments')
            ->add(TransformComment::class);

        /**
         * 在指定文章下发表评论
         *
         * @see Article::createComment()
         */
        $group->post('/articles/{article_id:\d+}/comments', Article::class . ':createComment')
            ->add(NeedLogin::class)
            ->add(TransformComment::class);

        /**
         * 获取已删除的文章
         *
         * @see Article::getDeleted()
         */
        $group->get('/trash/articles', Article::class . ':getDeleted')
            ->add(NeedManager::class);

        /**
         * 批量恢复文章
         *
         * @see Article::restoreMultiple()
         */
        $group->post('/trash/articles', Article::class . ':restoreMultiple')
            ->add(NeedManager::class);

        /**
         * 批量销毁文章
         *
         * @see Article::destroyMultiple()
         */
        $group->delete('/trash/articles', Article::class . ':destroyMultiple')
            ->add(NeedManager::class);

        /**
         * 恢复文章
         *
         * @see Article::restore()
         */
        $group->post('/trash/articles/{article_id:\d+}', Article::class . ':restore')
            ->add(NeedManager::class);

        /**
         * 销毁文章
         *
         * @see Article::destroy()
         */
        $group->delete('/trash/articles/{article_id:\d+}', Article::class . ':destroy')
            ->add(NeedManager::class);

        return $this;
    }

    /**
     * 评论
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function comment(RouteCollectorProxy $group): self
    {
        /**
         * 获取评论列表
         *
         * @see Comment::getList()
         */
        $group->get('/comments', Comment::class . ':getList')
            ->add(TransformComment::class);

        /**
         * 批量删除评论
         *
         * @see Comment::deleteMultiple()
         */
        $group->delete('/comments', Comment::class . ':deleteMultiple')
            ->add(NeedManager::class);

        /**
         * 获取评论
         *
         * @see Comment::get()
         */
        $group->get('/comments/{comment_id:\d+}', Comment::class . ':get')
            ->add(TransformComment::class);

        /**
         * 更新评论
         *
         * @see Comment::update()
         */
        $group->patch('/comments/{comment_id:\d+}', Comment::class . ':update')
            ->add(NeedLogin::class)
            ->add(TransformComment::class);

        /**
         * 删除评论
         *
         * @see Comment::delete()
         */
        $group->delete('/comments/{comment_id:\d+}', Comment::class . ':delete')
            ->add(NeedLogin::class);

        /**
         * 获取指定评论的投票者
         *
         * @see Comment::getVoters()
         */
        $group->get('/comments/{comment_id:\d+}/voters', Comment::class . ':getVoters')
            ->add(TransformUser::class);

        /**
         * 为指定评论添加投票
         *
         * @see Comment::addVote()
         */
        $group->post('/comments/{comment_id:\d+}/voters', Comment::class . ':addVote')
            ->add(NeedLogin::class);

        /**
         * 取消对指定评论的投票
         *
         * @see Comment::deleteVote()
         */
        $group->delete('/comments/{comment_id:\d+}/voters', Comment::class . ':deleteVote')
            ->add(NeedLogin::class);

        /**
         * 获取回收站中的评论
         *
         * @see Comment::getDeleted()
         */
        $group->get('/trash/comments', Comment::class . ':getDeleted')
            ->add(NeedManager::class)
            ->add(TransformComment::class);

        /**
         * 批量恢复评论
         *
         * @see Comment::restoreMultiple()
         */
        $group->post('/trash/comments', Comment::class . ':restoreMultiple')
            ->add(NeedManager::class);

        /**
         * 批量销毁评论
         *
         * @see Comment::destroyMultiple()
         */
        $group->delete('/trash/comments', Comment::class . ':destroyMultiple')
            ->add(NeedManager::class);

        /**
         * 恢复评论
         *
         * @see Comment::restore()
         */
        $group->post('/trash/comments/{comment_id:\d+}', Comment::class . ':restore')
            ->add(NeedManager::class)
            ->add(TransformComment::class);

        /**
         * 销毁评论
         *
         * @see Comment::destroy()
         */
        $group->delete('/trash/comments/{comment_id:\d+}', Comment::class . ':destroy')
            ->add(NeedManager::class);

        return $this;
    }

    /**
     * 举报
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function report(RouteCollectorProxy $group): self
    {
        /**
         * 获取举报列表
         *
         * @see Report::getList()
         */
        $group->get('/reports', Report::class . ':getList')
            ->add(NeedManager::class)
            ->add(TransformReport::class);

        /**
         * 批量删除举报
         *
         * @see Report::deleteMultiple()
         */
        $group->delete('/reports', Report::class . ':deleteMultiple')
            ->add(NeedManager::class);

        /**
         * 获取举报理由列表
         *
         * @see Report::getReasons()
         */
        $group->get('/reports/{reportable_type}/{reportable_id:\d+}', Report::class . ':getReasons')
            ->add(NeedManager::class)
            ->add(TransformReportReason::class);

        /**
         * 添加举报
         *
         * @see Report::create()
         */
        $group->post('/reports/{reportable_type}/{reportable_id:\d+}', Report::class . ':create')
            ->add(NeedLogin::class)
            ->add(TransformReportReason::class);

        /**
         * 删除举报
         *
         * @see Report::delete()
         */
        $group->delete('/reports/{reportable_type}/{reportable_id:\d+}', Report::class . ':delete')
            ->add(NeedManager::class);

        return $this;
    }

    /**
     * 私信
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function inbox(RouteCollectorProxy $group): self
    {

        return $this;
    }

    /**
     * 通知
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function notification(RouteCollectorProxy $group): self
    {

        return $this;
    }

    /**
     * 图形验证码
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function captcha(RouteCollectorProxy $group): self
    {
        /**
         * 创建图形验证码
         *
         * @see Captcha::create()
         */
        $group->post('/captchas', Captcha::class . ':create');

        return $this;
    }

    /**
     * 邮件
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function email(RouteCollectorProxy $group): self
    {
        /**
         * 发送邮件
         *
         * @see Email::send()
         */
        $group->post('/emails', Email::class . ':send')
            ->add(NeedManager::class);

        return $this;
    }

    /**
     * 图片
     *
     * @param  RouteCollectorProxy $group
     * @return RestApi
     */
    protected function image(RouteCollectorProxy $group): self
    {
        /**
         * 获取图片列表
         *
         * @see Image::getList()
         */
        $group->get('/images', Image::class . ':getList')
            ->add(NeedManager::class)
            ->add(TransformImage::class);

        /**
         * 上传图片
         *
         * @see Image::upload()
         */
        $group->post('/images', Image::class . ':upload')
            ->add(NeedLogin::class)
            ->add(TransformImage::class);

        /**
         * 批量删除图片
         *
         * @see Image::deleteMultiple()
         */
        $group->delete('/images', Image::class . ':deleteMultiple')
            ->add(NeedManager::class);

        /**
         * 获取图片
         *
         * @see Image::get()
         */
        $group->get('/images/{key}', Image::class . ':get')
            ->add(TransformImage::class);

        /**
         * 更新图片
         *
         * @see Image::update()
         */
        $group->patch('/images/{key}', Image::class . ':update')
            ->add(NeedManager::class)
            ->add(TransformImage::class);

        /**
         * 删除图片
         *
         * @see Image::delete()
         */
        $group->delete('/images/{key}', Image::class . ':delete')
            ->add(NeedManager::class);

        return $this;
    }
}
