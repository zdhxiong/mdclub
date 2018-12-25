<?php

declare(strict_types=1);

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

// 页面
$app->get(   '/',                             IndexController::class .        ':pageIndex');
$app->get(   '/migration',                    IndexController::class .        ':migration');
$app->get(   '/update_statistics',            IndexController::class .        ':statistics');
$app->get(   '/topics',                       TopicController::class .        ':pageIndex');
$app->get(   '/topics/{topic_id:\d+}',        TopicController::class .        ':pageInfo');
$app->get(   '/articles',                     ArticleController::class .      ':pageIndex');
$app->get(   '/articles/{article_id:\d+}',    ArticleController::class .      ':pageInfo');
$app->get(   '/questions',                    QuestionController::class .     ':pageIndex');
$app->get(   '/questions/{question_id:\d+}',  QuestionController::class .     ':pageInfo');
$app->get(   '/users',                        UserController::class .         ':pageIndex');
$app->get(   '/users/{user_id:\d+}',          UserController::class .         ':pageInfo');
$app->get(   '/notifications',                NotificationController::class . ':pageIndex');
$app->get(   '/inbox',                        InboxController::class .        ':pageIndex');

// api
$app->group('/api', function () {
    $this->get(   '',                                                           ApiController::class . ':pageIndex');

    // 系统设置
    $this->get(   '/options',                                                   OptionController::class . ':getAll');
    $this->patch( '/options',                                                   OptionController::class . ':updateMultiple');

    // 登录
    $this->post(  '/tokens',                                                    TokenController::class . ':create');

    // 注册
    $this->post(  '/users',                                                     UserController::class . ':create');
    $this->post(  '/user/register/email',                                       UserController::class . ':sendRegisterEmail');

    // 重置密码
    $this->post(  '/user/password/email',                                       UserController::class . ':sendResetEmail');
    $this->put(   '/user/password',                                             UserController::class . ':updatePasswordByEmail');

    // 用户信息
    $this->get(   '/users',                                                     UserController::class . ':getList');
    $this->delete('/users',                                                     UserController::class . ':disableMultiple');
    $this->get(   '/users/{user_id:\d+}',                                       UserController::class . ':getOne');
    $this->patch( '/users/{user_id:\d+}',                                       UserController::class . ':updateOne');
    $this->delete('/users/{user_id:\d+}',                                       UserController::class . ':disableOne');
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
    $this->post(  '/users/{user_id:\d+}/followers',                             UserController::class . ':addFollow');
    $this->delete('/users/{user_id:\d+}/followers',                             UserController::class . ':deleteFollow');
    $this->get(   '/users/{user_id:\d+}/followees',                             UserController::class . ':getFollowees');
    $this->get(   '/user/followers',                                            UserController::class . ':getMyFollowers');
    $this->get(   '/user/followees',                                            UserController::class . ':getMyFollowees');

    // 禁用的用户
    $this->get(   '/trash/users',                                               UserController::class . ':getDisabledList');
    $this->post(  '/trash/users',                                               UserController::class . ':enableMultiple');
    $this->post(  '/trash/users/{user_id:\d+}',                                 UserController::class . ':enableOne');

    // 话题信息
    $this->get(   '/topics',                                                    TopicController::class . ':getList');
    $this->post(  '/topics',                                                    TopicController::class . ':create');
    $this->delete('/topics',                                                    TopicController::class . ':deleteMultiple');
    $this->get(   '/topics/{topic_id:\d+}',                                     TopicController::class . ':getOne');
    $this->post(  '/topics/{topic_id:\d+}',                                     TopicController::class . ':updateOne'); // formData 数据只能通过 post 请求提交，所以这里不用 patch 请求
    $this->delete('/topics/{topic_id:\d+}',                                     TopicController::class . ':deleteOne');

    // 话题关注
    $this->get(   '/users/{user_id:\d+}/following_topics',                      TopicController::class . ':getFollowing');
    $this->get(   '/user/following_topics',                                     TopicController::class . ':getMyFollowing');
    $this->get(   '/topics/{topic_id:\d+}/followers',                           TopicController::class . ':getFollowers');
    $this->post(  '/topics/{topic_id:\d+}/followers',                           TopicController::class . ':addFollow');
    $this->delete('/topics/{topic_id:\d+}/followers',                           TopicController::class . ':deleteFollow');

    // 话题回收站
    $this->get(   '/trash/topics',                                              TopicController::class . ':getDeletedList');
    $this->post(  '/trash/topics',                                              TopicController::class . ':restoreMultiple');
    $this->delete('/trash/topics',                                              TopicController::class . ':destroyMultiple');
    $this->post(  '/trash/topics/{topic_id:\d+}',                               TopicController::class . ':restoreOne');
    $this->delete('/trash/topics/{topic_id:\d+}',                               TopicController::class . ':destroyOne');

    // 问题信息
    $this->get(   '/users/{user_id:\d+}/questions',                             QuestionController::class . ':getListByUserId');
    $this->get(   '/user/questions',                                            QuestionController::class . ':getMyList');
    $this->get(   '/topics/{topic_id:\d+}/questions',                           QuestionController::class . ':getListByTopicId');
    $this->get(   '/questions',                                                 QuestionController::class . ':getList');
    $this->post(  '/questions',                                                 QuestionController::class . ':create');
    $this->delete('/questions',                                                 QuestionController::class . ':deleteMultiple');
    $this->get(   '/questions/{question_id:\d+}',                               QuestionController::class . ':getOne');
    $this->patch( '/questions/{question_id:\d+}',                               QuestionController::class . ':updateOne');
    $this->delete('/questions/{question_id:\d+}',                               QuestionController::class . ':deleteOne');

    // 问题评论
    $this->get(   '/questions/{question_id:\d+}/comments',                      QuestionController::class . ':getComments');
    $this->post(  '/questions/{question_id:\d+}/comments',                      QuestionController::class . ':addComment');

    // 问题投票
    $this->get(   '/questions/{question_id:\d+}/voters',                        QuestionController::class . ':getVoters');
    $this->post(  '/questions/{question_id:\d+}/voters',                        QuestionController::class . ':addVote');
    $this->delete('/questions/{question_id:\d+}/voters',                        QuestionController::class . ':deleteVote');

    // 问题关注
    $this->get(   '/users/{user_id:\d+}/following_questions',                   QuestionController::class . ':getFollowing');
    $this->get(   '/user/following_questions',                                  QuestionController::class . ':getMyFollowing');
    $this->get(   '/questions/{question_id:\d+}/followers',                     QuestionController::class . ':getFollowers');
    $this->post(  '/questions/{question_id:\d+}/followers',                     QuestionController::class . ':addFollow');
    $this->delete('/questions/{question_id:\d+}/followers',                     QuestionController::class . ':deleteFollow');

    // 问题回收站
    $this->get(   '/trash/questions',                                           QuestionController::class . ':getDeletedList');
    $this->post(  '/trash/questions',                                           QuestionController::class . ':restoreMultiple');
    $this->delete('/trash/questions',                                           QuestionController::class . ':destroyMultiple');
    $this->post(  '/trash/questions/{question_id:\d+}',                         QuestionController::class . ':restoreOne');
    $this->delete('/trash/questions/{question_id:\d+}',                         QuestionController::class . ':destroyOne');

    // 回答信息
    $this->get(   '/users/{user_id:\d+}/answers',                               AnswerController::class . ':getListByUserId');
    $this->get(   '/user/answers',                                              AnswerController::class . ':getMyList');
    $this->get(   '/questions/{question_id:\d+}/answers',                       AnswerController::class . ':getListByQuestionId');
    $this->post(  '/questions/{question_id:\d+}/answers',                       AnswerController::class . ':create');
    $this->get(   '/answers',                                                   AnswerController::class . ':getList');
    $this->delete('/answers',                                                   AnswerController::class . ':deleteMultiple');
    $this->get(   '/answers/{answer_id:\d+}',                                   AnswerController::class . ':getOne');
    $this->patch( '/answers/{answer_id:\d+}',                                   AnswerController::class . ':updateOne');
    $this->delete('/answers/{answer_id:\d+}',                                   AnswerController::class . ':deleteOne');

    // 回答评论
    $this->get(   '/answers/{answer_id:\d+}/comments',                          AnswerController::class . ':getComments');
    $this->post(  '/answers/{answer_id:\d+}/comments',                          AnswerController::class . ':addComment');

    // 回答投票
    $this->get(   '/answers/{answer_id:\d+}/voters',                            AnswerController::class . ':getVoters');
    $this->post(  '/answers/{answer_id:\d+}/voters',                            AnswerController::class . ':addVote');
    $this->delete('/answers/{answer_id:\d+}/voters',                            AnswerController::class . ':deleteVote');

    // 回答回收站
    $this->get(   '/trash/answers',                                             AnswerController::class . ':getDeletedList');
    $this->post(  '/trash/answers',                                             AnswerController::class . ':restoreMultiple');
    $this->delete('/trash/answers',                                             AnswerController::class . ':destroyMultiple');
    $this->post(  '/trash/answers/{answer_id:\d+}',                             AnswerController::class . ':restoreOne');
    $this->delete('/trash/answers/{answer_id:\d+}',                             AnswerController::class . ':destroyOne');

    // 文章信息
    $this->get(   '/users/{user_id:\d+}/articles',                              ArticleController::class . ':getListByUserId');
    $this->get(   '/user/articles',                                             ArticleController::class . ':getMyList');
    $this->get(   '/topics/{topic_id}/articles',                                ArticleController::class . ':getListByTopicId');
    $this->get(   '/articles',                                                  ArticleController::class . ':getList');
    $this->post(  '/articles',                                                  ArticleController::class . ':create');
    $this->delete('/articles',                                                  ArticleController::class . ':deleteMultiple');
    $this->get(   '/articles/{article_id:\d+}',                                 ArticleController::class . ':getOne');
    $this->patch( '/articles/{article_id:\d+}',                                 ArticleController::class . ':updateOne');
    $this->delete('/articles/{article_id:\d+}',                                 ArticleController::class . ':deleteOne');

    // 文章评论
    $this->get(   '/articles/{article_id:\d+}/comments',                        ArticleController::class . ':getComments');
    $this->post(  '/articles/{article_id:\d+}/comments',                        ArticleController::class . ':addComment');

    // 文章投票
    $this->get(   '/articles/{article_id:\d+}/voters',                          ArticleController::class . ':getVoters');
    $this->post(  '/articles/{article_id:\d+}/voters',                          ArticleController::class . ':addVote');
    $this->delete('/articles/{article_id:\d+}/voters',                          ArticleController::class . ':deleteVote');

    // 文章关注
    $this->get(   '/users/{user_id:\d+}/following_articles',                    ArticleController::class . ':getFollowing');
    $this->get(   '/user/following_articles',                                   ArticleController::class . ':getMyFollowing');
    $this->get(   '/articles/{article_id:\d+}/followers',                       ArticleController::class . ':getFollowers');
    $this->post(  '/articles/{article_id:\d+}/followers',                       ArticleController::class . ':addFollow');
    $this->delete('/articles/{article_id:\d+}/followers',                       ArticleController::class . ':deleteFollow');

    // 文章回收站
    $this->get(   '/trash/articles',                                            ArticleController::class . ':getDeletedList');
    $this->post(  '/trash/articles',                                            ArticleController::class . ':restoreMultiple');
    $this->delete('/trash/articles',                                            ArticleController::class . ':destroyMultiple');
    $this->post(  '/trash/articles/{article_id:\d+}',                           ArticleController::class . ':restoreOne');
    $this->delete('/trash/articles/{article_id:\d+}',                           ArticleController::class . ':destroyOne');

    // 评论
    $this->get(   '/users/{user_id:\d+}/comments',                              CommentController::class . ':getListByUserId');
    $this->get(   '/user/comments',                                             CommentController::class . ':getMyList');
    $this->get(   '/comments',                                                  CommentController::class . ':getList');
    $this->delete('/comments',                                                  CommentController::class . ':deleteMultiple');
    $this->get(   '/comments/{comment_id:\d+}',                                 CommentController::class . ':getOne');
    $this->patch( '/comments/{comment_id:\d+}',                                 CommentController::class . ':updateOne');
    $this->delete('/comments/{comment_id:\d+}',                                 CommentController::class . ':deleteOne');

    // 评论投票
    $this->get(   '/comments/{comment_id:\d+}/voters',                          CommentController::class . ':getVoters');
    $this->post(  '/comments/{comment_id:\d+}/voters',                          CommentController::class . ':addVote');
    $this->delete('/comments/{comment_id:\d+}/voters',                          CommentController::class . ':deleteVote');

    // 评论回收站
    $this->get(   '/trash/comments',                                            CommentController::class . ':getDeletedList');
    $this->post(  '/trash/comments',                                            CommentController::class . ':restoreMultiple');
    $this->delete('/trash/comments',                                            CommentController::class . ':destroyMultiple');
    $this->post(  '/trash/comments/{comment_id:\d+}',                           CommentController::class . ':restoreOne');
    $this->delete('/trash/comments/{comment_id:\d+}',                           CommentController::class . ':destroyOne');

    // 举报
    $this->get(   '/reports',                                                   ReportController::class . ':getList');
    $this->delete('/reports',                                                   ReportController::class . ':deleteMultiple');
    $this->get(   '/reports/{reportable_type}/{reportable_id:\d+}',             ReportController::class . ':getDetailList');
    $this->post(  '/reports/{reportable_type}/{reportable_id:\d+}',             ReportController::class . ':create');
    $this->delete('/reports/{reportable_type}/{reportable_id:\d+}',             ReportController::class . ':deleteOne');

    // 图形验证码
    $this->post(  '/captchas',                                                  CaptchaController::class . ':create');

    // 邮件
    $this->post(  '/emails',                                                    EmailController::class . ':send');

    // 图片
    $this->get(   '/images',                                                    ImageController::class . ':getList');
    $this->post(  '/images',                                                    ImageController::class . ':upload');
    $this->delete('/images',                                                    ImageController::class . ':deleteMultiple');
    $this->get(   '/images/{hash}',                                             ImageController::class . ':getOne');
    $this->patch( '/images/{hash}',                                             ImageController::class . ':updateOne');
    $this->delete('/images/{hash}',                                             ImageController::class . ':deleteOne');
})->add(new \App\Middleware\EnableCrossRequestMiddleware());

// admin
$app->group('/admin', function () {
    $this->get('',                   AdminController::class . ':pageIndex');
    $this->get('/{name}',            AdminController::class . ':pageIndex');
    $this->get('/{name}/{sub_name}', AdminController::class . ':pageIndex');
});
