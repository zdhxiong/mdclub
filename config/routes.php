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

    $this->get(   '/options',                                                   OptionController::class . ':getAll');
    $this->patch( '/options',                                                   OptionController::class . ':updateAll');

    $this->post(  '/tokens',                                                    TokenController::class . ':create');

    $this->get(   '/users',                                                     UserController::class . ':getList');
    $this->post(  '/users',                                                     UserController::class . ':create');
    $this->delete('/users',                                                     UserController::class . ':batchDisable');
    $this->get(   '/users/{user_id:\d+}',                                       UserController::class . ':get');
    $this->patch( '/users/{user_id:\d+}',                                       UserController::class . ':update');
    $this->delete('/users/{user_id:\d+}',                                       UserController::class . ':disable');
    $this->delete('/users/{user_id:\d+}/avatar',                                UserController::class . ':deleteAvatar');
    $this->delete('/users/{user_id:\d+}/cover',                                 UserController::class . ':deleteCover');
    $this->get(   '/users/{user_id:\d+}/followers',                             UserController::class . ':getFollowers');
    $this->get(   '/users/{user_id:\d+}/followees',                             UserController::class . ':getFollowees');
    $this->get(   '/users/{user_id:\d+}/following/questions',                   QuestionController::class . ':getFollowing');
    $this->get(   '/users/{user_id:\d+}/following/articles',                    ArticleController::class .  ':getFollowing');
    $this->get(   '/users/{user_id:\d+}/following/topics',                      TopicController::class .    ':getFollowing');

    $this->get(   '/user',                                                      UserController::class . ':getMe');
    $this->patch( '/user',                                                      UserController::class . ':updateMe');
    $this->post(  '/user/avatar',                                               UserController::class . ':uploadMyAvatar');
    $this->delete('/user/avatar',                                               UserController::class . ':deleteMyAvatar');
    $this->post(  '/user/cover',                                                UserController::class . ':uploadMyCover');
    $this->delete('/user/cover',                                                UserController::class . ':deleteMyCover');
    $this->post(  '/user/register/email',                                       UserController::class . ':sendRegisterEmail');
    $this->post(  '/user/password/email',                                       UserController::class . ':sendResetEmail');
    $this->put(   '/user/password',                                             UserController::class . ':updatePasswordByEmail');
    $this->get(   '/user/followers',                                            UserController::class . ':getMyFollowers');
    $this->get(   '/user/followees',                                            UserController::class . ':getMyFollowees');
    $this->put(   '/user/following/{target_user_id:\d+}',                       UserController::class . ':addFollow');
    $this->delete('/user/following/{target_user_id:\d+}',                       UserController::class . ':deleteFollow');
    $this->get(   '/user/following/questions',                                  QuestionController::class . ':getMyFollowing');
    $this->put(   '/user/following/questions/{question_id:\d+}',                QuestionController::class . ':addFollow');
    $this->delete('/user/following/questions/{question_id:\d+}',                QuestionController::class . ':deleteFollow');
    $this->get(   '/user/following/articles',                                   ArticleController::class .  ':getMyFollowing');
    $this->put(   '/user/following/articles/{article_id:\d+}',                  ArticleController::class .  ':addFollow');
    $this->delete('/user/following/articles/{article_id:\d+}',                  ArticleController::class .  ':deleteFollow');
    $this->get(   '/user/following/topics',                                     TopicController::class .    ':getMyFollowing');
    $this->put(   '/user/following/topics/{topic_id:\d+}',                      TopicController::class .    ':addFollow');
    $this->delete('/user/following/topics/{topic_id:\d+}',                      TopicController::class .    ':deleteFollow');

    $this->get(   '/articles',                                                  ArticleController::class . ':getList');
    $this->post(  '/articles',                                                  ArticleController::class . ':create');
    $this->get(   '/articles/{article_id:\d+}',                                 ArticleController::class . ':get');
    $this->patch( '/articles/{article_id:\d+}',                                 ArticleController::class . ':update');
    $this->delete('/articles/{article_id:\d+}',                                 ArticleController::class . ':delete');
    $this->post(  '/articles/{article_id:\d+}/vote',                            ArticleController::class . ':vote');
    $this->get(   '/articles/{article_id:\d+}/followers',                       ArticleController::class . ':getFollowers');
    $this->get(   '/articles/{article_id:\d+}/comments',                        ArticleController::class . ':getComments');
    $this->post(  '/articles/{article_id:\d+}/comments',                        ArticleController::class . ':createComment');

    $this->get(   '/questions',                                                 QuestionController::class . ':getList');
    $this->post(  '/questions',                                                 QuestionController::class . ':create');
    $this->get(   '/questions/{question_id:\d+}',                               QuestionController::class . ':get');
    $this->patch( '/questions/{question_id:\d+}',                               QuestionController::class . ':update');
    $this->delete('/questions/{question_id:\d+}',                               QuestionController::class . ':delete');
    $this->post(  '/questions/{question_id:\d+}/votes',                         QuestionController::class . ':vote');
    $this->get(   '/questions/{question_id:\d+}/followers',                     QuestionController::class . ':getFollowers');
    $this->get(   '/questions/{question_id:\d+}/comments',                      QuestionController::class . ':getComments');
    $this->post(  '/questions/{question_id:\d+}/comments',                      QuestionController::class . ':createComment');
    $this->get(   '/questions/{question_id:\d+}/answers',                       AnswerController::class .   ':getListByQuestionId');
    $this->post(  '/questions/{question_id:\d+}/answers',                       AnswerController::class .   ':create');

    $this->get(   '/topics',                                                    TopicController::class . ':getList');
    $this->post(  '/topics',                                                    TopicController::class . ':create');
    $this->delete('/topics',                                                    TopicController::class . ':batchDelete');
    $this->get(   '/topics/{topic_id:\d+}',                                     TopicController::class . ':get');
    $this->post(  '/topics/{topic_id:\d+}',                                     TopicController::class . ':update'); // formData 数据只能通过 post 请求提交，所以这里不用 patch 请求
    $this->delete('/topics/{topic_id:\d+}',                                     TopicController::class . ':delete');
    $this->get(   '/topics/{topic_id:\d+}/followers',                           TopicController::class . ':getFollowers');

    $this->get(   '/answers',                                                   AnswerController::class . ':getList');
    $this->get(   '/answers/{answer_id:\d+}',                                   AnswerController::class . ':get');
    $this->patch( '/answers/{answer_id:\d+}',                                   AnswerController::class . ':update');
    $this->delete('/answers/{answer_id:\d+}',                                   AnswerController::class . ':delete');
    $this->post(  '/answers/{answer_id:\d+}/votes',                             AnswerController::class . ':vote');
    $this->get(   '/answers/{answer_id:\d+}/comments',                          AnswerController::class . ':getComments');
    $this->post(  '/answers/{answer_id:\d+}/comments',                          AnswerController::class . ':createComment');

    $this->get(   '/comments',                                                  CommentController::class . ':getList');
    $this->get(   '/comments/{comment_id:\d+}',                                 CommentController::class . ':get');
    $this->patch( '/comments/{comment_id:\d+}',                                 CommentController::class . ':update');
    $this->delete('/comments/{comment_id:\d+}',                                 CommentController::class . ':delete');
    $this->post(  '/comments/{comment_id:\d+}/vote',                            CommentController::class . ':vote');

    $this->get(   '/reports',                                                   ReportController::class . ':getList');
    $this->post(  '/reports',                                                   ReportController::class . ':create');
    $this->delete('/reports/{report_id:\d+}',                                   ReportController::class . ':delete');

    $this->post(  '/images',                                                    ImageController::class . ':upload');
    $this->post(  '/emails',                                                    EmailController::class . ':send');
    $this->post(  '/captchas',                                                  CaptchaController::class . ':create');

    $this->delete('/trash',                                                     TrashController::class . ':deleteAll');
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
