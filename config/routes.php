<?php

declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Controller\AdminController;
use App\Controller\AnswerController;
use App\Controller\ApiController;
use App\Controller\ArticleController;
use App\Controller\CaptchaController;
use App\Controller\CommentController;
use App\Controller\EmailController;
use App\Controller\ImageController;
use App\Controller\InboxController;
use App\Controller\IndexController;
use App\Controller\NotificationController;
use App\Controller\OptionController;
use App\Controller\QuestionController;
use App\Controller\ReportController;
use App\Controller\TopicController;
use App\Controller\UserController;
use App\Controller\TokenController;
use App\Controller\TrashController;

// 页面
$app->get(   '/',                             IndexController::class .        ':pageIndex');
$app->get(   '/migration',                    IndexController::class .        ':migration');
$app->get(   '/topics',                       TopicController::class .        ':pageIndex');
$app->get(   '/topics/{topic_id:\d+}',        TopicController::class .        ':pageDetail');
$app->get(   '/articles',                     ArticleController::class .      ':pageIndex');
$app->get(   '/articles/{article_id:\d+}',    ArticleController::class .      ':pageDetail');
$app->get(   '/questions',                    QuestionController::class .     ':pageIndex');
$app->get(   '/questions/{question_id:\d+}',  QuestionController::class .     ':pageDetail');
$app->get(   '/users',                        UserController::class .         ':pageIndex');
$app->get(   '/users/{user_id:\d+}',          UserController::class .         ':pageDetail');
$app->get(   '/notifications',                NotificationController::class . ':pageIndex');
$app->get(   '/inbox',                        InboxController::class .        ':pageIndex');

$app->group('/api', function () {
    $this->get(   '',                                                           ApiController::class . ':pageIndex');

    // 系统设置
    $this->get(   '/options',                                                   OptionController::class . ':getAll');
    $this->patch( '/options',                                                   OptionController::class . ':setMultiple');

    // 登录
    $this->post(  '/tokens',                                                    TokenController::class . ':create');

    // 注册
    $this->post(  '/user/register/email',                                       UserController::class . ':sendRegisterEmail');
    $this->post(  '/users',                                                     UserController::class . ':create');

    // 重置密码
    $this->post(  '/user/password/email',                                       UserController::class . ':sendResetEmail');
    $this->put(   '/user/password',                                             UserController::class . ':updatePasswordByEmail');

    // 用户信息
    $this->get(   '/users',                                                     UserController::class . ':getList');
    $this->get(   '/users/{user_id:\d+}',                                       UserController::class . ':get');
    $this->patch( '/users/{user_id:\d+}',                                       UserController::class . ':update');
    $this->delete('/users/{user_id:\d+}',                                       UserController::class . ':disable');
    $this->get(   '/user',                                                      UserController::class . ':getMe');
    $this->patch( '/user',                                                      UserController::class . ':updateMe');

    // 用户头像
    $this->delete('/users/{user_id:\d+}/avatar',                                UserController::class . ':deleteAvatar');
    $this->post(  '/user/avatar',                                               UserController::class . ':uploadMyAvatar');
    $this->delete('/user/avatar',                                               UserController::class . ':deleteMyAvatar');

    // 用户封面
    $this->delete('/users/{user_id:\d+}/cover',                                 UserController::class . ':deleteCover');
    $this->post(  '/user/cover',                                                UserController::class . ':uploadMyCover');
    $this->delete('/user/cover',                                                UserController::class . ':deleteMyCover');

    // 用户关注
    $this->get(   '/users/{user_id:\d+}/followers',                             UserController::class . ':getFollowers');
    $this->get(   '/users/{user_id:\d+}/followees',                             UserController::class . ':getFollowing');
    $this->get(   '/users/{user_id:\d+}/following/{target_user_id:\d+}',        UserController::class . ':isFollowing');
    $this->get(   '/user/followers',                                            UserController::class . ':getMyFollowers');
    $this->get(   '/user/followees',                                            UserController::class . ':getMyFollowing');
    $this->put(   '/user/following/{target_user_id:\d+}',                       UserController::class . ':addFollow');
    $this->delete('/user/following/{target_user_id:\d+}',                       UserController::class . ':deleteFollow');
    $this->get(   '/user/following/{target_user_id:\d+}',                       UserController::class . ':isMyFollowing');

    // 话题信息
    $this->get(   '/topics',                                                    TopicController::class . ':getList');
    $this->post(  '/topics',                                                    TopicController::class . ':create');
    $this->get(   '/topics/{topic_id:\d+}',                                     TopicController::class . ':get');
    $this->post(  '/topics/{topic_id:\d+}',                                     TopicController::class . ':update'); // formData 数据只能通过 post 请求提交，所以这里不用 patch 请求
    $this->delete('/topics/{topic_id:\d+}',                                     TopicController::class . ':delete');

    // 话题关注
    $this->get(   '/users/{user_id:\d+}/following/topics',                      TopicController::class . ':getFollowing');
    $this->get(   '/users/{user_id:\d+}/following/topics/{topic_id:\d+}',       TopicController::class . ':isFollowing');
    $this->get(   '/user/following/topics',                                     TopicController::class . ':getMyFollowing');
    $this->get(   '/user/following/topics/{topic_id:\d+}',                      TopicController::class . ':isMyFollowing');
    $this->put(   '/user/following/topics/{topic_id:\d+}',                      TopicController::class . ':addFollow');
    $this->delete('/user/following/topics/{topic_id:\d+}',                      TopicController::class . ':deleteFollow');
    $this->get(   '/topics/{topic_id:\d+}/followers',                           TopicController::class . ':getFollowers');

    // 问题信息
    $this->get(   '/questions',                                                 QuestionController::class . ':getList');
    $this->post(  '/questions',                                                 QuestionController::class . ':create');
    $this->get(   '/questions/{question_id:\d+}',                               QuestionController::class . ':get');
    $this->patch( '/questions/{question_id:\d+}',                               QuestionController::class . ':update');
    $this->delete('/questions/{question_id:\d+}',                               QuestionController::class . ':delete');
    $this->post(  '/questions/{question_id:\d+}/votes',                         QuestionController::class . ':vote');
    $this->get(   '/questions/{question_id:\d+}/comments',                      QuestionController::class . ':getComments');
    $this->post(  '/questions/{question_id:\d+}/comments',                      QuestionController::class . ':createComment');

    // 问题关注
    $this->get(   '/users/{user_id:\d+}/following/questions',                   QuestionController::class . ':getFollowing');
    $this->get(   '/users/{user_id:\d+}/following/questions/{question_id:\d+}', QuestionController::class . ':isFollowing');
    $this->get(   '/user/following/questions',                                  QuestionController::class . ':getMyFollowing');
    $this->get(   '/questions/{question_id:\d+}/following',                     QuestionController::class . ':isMyFollowing');
    $this->put(   '/user/following/questions/{question_id:\d+}',                QuestionController::class . ':addFollow');
    $this->delete('/user/following/questions/{question_id:\d+}',                QuestionController::class . ':deleteFollow');
    $this->get(   '/user/following/questions/{question_id:\d+}',                QuestionController::class . ':getFollowers');

    // 回答
    $this->get(   '/questions/{question_id:\d+}/answers',                       AnswerController::class . ':getListByQuestionId');
    $this->post(  '/questions/{question_id:\d+}/answers',                       AnswerController::class . ':create');
    $this->get(   '/answers',                                                   AnswerController::class . ':getList');
    $this->get(   '/answers/{answer_id:\d+}',                                   AnswerController::class . ':get');
    $this->patch( '/answers/{answer_id:\d+}',                                   AnswerController::class . ':update');
    $this->delete('/answers/{answer_id:\d+}',                                   AnswerController::class . ':delete');
    $this->post(  '/answers/{answer_id:\d+}/votes',                             AnswerController::class . ':vote');
    $this->get(   '/answers/{answer_id:\d+}/comments',                          AnswerController::class . ':getComments');
    $this->post(  '/answers/{answer_id:\d+}/comments',                          AnswerController::class . ':createComment');

    // 附言

    // 文章
    $this->get(   '/articles',                                                  ArticleController::class . ':getList');
    $this->post(  '/articles',                                                  ArticleController::class . ':create');
    $this->get(   '/articles/{article_id:\d+}',                                 ArticleController::class . ':get');
    $this->patch( '/articles/{article_id:\d+}',                                 ArticleController::class . ':update');
    $this->delete('/articles/{article_id:\d+}',                                 ArticleController::class . ':delete');
    $this->post(  '/articles/{article_id:\d+}/vote',                            ArticleController::class . ':vote');
    $this->get(   '/articles/{article_id:\d+}/comments',                        ArticleController::class . ':getComments');
    $this->post(  '/articles/{article_id:\d+}/comments',                        ArticleController::class . ':createComment');

    // 文章关注
    $this->get(   '/users/{user_id:\d+}/following/articles',                    ArticleController::class . ':getFollowing');
    $this->get(   '/users/{user_id:\d+}/following/articles/{article_id:\d+}',   ArticleController::class . ':isFollowing');
    $this->get(   '/user/following/articles',                                   ArticleController::class . ':getMyFollowing');
    $this->get(   '/user/following/articles/{article_id:\d+}',                  ArticleController::class . ':isMyFollowing');
    $this->put(   '/user/following/articles/{article_id:\d+}',                  ArticleController::class . ':addFollow');
    $this->delete('/user/following/articles/{article_id:\d+}',                  ArticleController::class . ':deleteFollow');
    $this->get(   '/articles/{article_id:\d+}/followers',                       ArticleController::class . ':getFollowers');

    // 评论
    $this->get(   '/comments',                                                  CommentController::class . ':getList');
    $this->get(   '/comments/{comment_id:\d+}',                                 CommentController::class . ':get');
    $this->patch( '/comments/{comment_id:\d+}',                                 CommentController::class . ':update');
    $this->delete('/comments/{comment_id:\d+}',                                 CommentController::class . ':delete');
    $this->post(  '/comments/{comment_id:\d+}/vote',                            CommentController::class . ':vote');

    // 举报
    $this->get(   '/reports',                                                   ReportController::class . ':getList');
    $this->get(   '/reports',                                                   ReportController::class . ':create');
    $this->delete('/reports/{report_id:\d+}',                                   ReportController::class . ':delete');

    // 私信

    // 通知

    // 验证码
    $this->post(  '/captcha',                                                   CaptchaController::class . ':create');

    // 邮件
    $this->post(  '/email',                                                     EmailController::class . ':send');

    // 图片
    $this->post(  '/images',                                                    ImageController::class . ':upload');

    // 回收站
    $this->get(   '/trash/users',                                               TrashController::class . ':getUsers');
    $this->post(  '/trash/users/{user_id:\d+}',                                 TrashController::class . ':restoreUser');

    $this->get(   '/trash/topics',                                              TrashController::class . ':getTopics');
    $this->post(  '/trash/topics/{topic_id:\d+}',                               TrashController::class . ':restoreTopic');
    $this->delete('/trash/topics/{topic_id:\d+}',                               TrashController::class . ':deleteTopic');

    $this->get(   '/trash/questions',                                           TrashController::class . ':getQuestions');
    $this->post(  '/trash/questions/{question_id:\d+}',                         TrashController::class . ':restoreQuestion');
    $this->delete('/trash/questions/{question_id:\d+}',                         TrashController::class . ':deleteQuestion');

    $this->get(   '/trash/answers',                                             TrashController::class . ':getAnswers');
    $this->post(  '/trash/answers/{answer_id:\d+}',                             TrashController::class . ':restoreAnswer');
    $this->delete('/trash/answers/{answer_id:\d+}',                             TrashController::class . ':deleteAnswer');

    $this->get(   '/trash/articles',                                            TrashController::class . ':getArticles');
    $this->post(  '/trash/articles/{article_id:\d+}',                           TrashController::class . ':restoreArticle');
    $this->delete('/trash/articles/{article_id:\d+}',                           TrashController::class . ':deleteArticle');

    $this->get(   '/trash/comments',                                            TrashController::class . ':getComments');
    $this->post(  '/trash/comments/{comment_id:\d+}',                           TrashController::class . ':restoreComment');
    $this->delete('/trash/comments/{comment_id:\d+}',                           TrashController::class . ':deleteComment');
})
    ->add(function (ServerRequestInterface $request, ResponseInterface $response, callable $next) {
        /** @var ResponseInterface $response */
        $response = $next($request, $response);

        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'Token, Origin, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie')
            ->withHeader('Access-Control-Allow-Methods', 'OPTIONS, GET, POST, PATCH, DELETE');
    });

// admin
$app->group('/admin', function () {
    $this->get('', AdminController::class . ':pageIndex');
});
