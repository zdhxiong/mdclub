<?php

declare(strict_types=1);

use App\Controller\Admin;
use App\Controller\Answer;
use App\Controller\Api;
use App\Controller\Article;
use App\Controller\Captcha;
use App\Controller\Comment;
use App\Controller\Email;
use App\Controller\Image;
use App\Controller\Inbox;
use App\Controller\Index;
use App\Controller\Notification;
use App\Controller\Option;
use App\Controller\Question;
use App\Controller\Report;
use App\Controller\Rss;
use App\Controller\Topic;
use App\Controller\User;
use App\Controller\Token;
use App\Middleware\EnableCrossRequest;

// 页面
$app->get(   '/',                             Index::class .        ':pageIndex');
$app->get(   '/migration',                    Index::class .        ':migration');
$app->get(   '/update_statistics',            Index::class .        ':statistics');
$app->get(   '/topics',                       Topic::class .        ':pageIndex');
$app->get(   '/topics/{topic_id:\d+}',        Topic::class .        ':pageInfo');
$app->get(   '/articles',                     Article::class .      ':pageIndex');
$app->get(   '/articles/{article_id:\d+}',    Article::class .      ':pageInfo');
$app->get(   '/questions',                    Question::class .     ':pageIndex');
$app->get(   '/questions/{question_id:\d+}',  Question::class .     ':pageInfo');
$app->get(   '/users',                        User::class .         ':pageIndex');
$app->get(   '/users/{user_id:\d+}',          User::class .         ':pageInfo');
$app->get(   '/notifications',                Notification::class . ':pageIndex');
$app->get(   '/inbox',                        Inbox::class .        ':pageIndex');

// api
$app->group('/api', function () {
    $this->get(   '',                                                 Api::class . ':pageIndex');

    // 系统设置
    $this->get(   '/options',                                         Option::class . ':getAll');
    $this->patch( '/options',                                         Option::class . ':updateMultiple');

    // 登录
    $this->post(  '/tokens',                                          Token::class . ':create');

    // 注册
    $this->post(  '/users',                                           User::class . ':create');
    $this->post(  '/user/register/email',                             User::class . ':sendRegisterEmail');

    // 重置密码
    $this->post(  '/user/password/email',                             User::class . ':sendResetEmail');
    $this->put(   '/user/password',                                   User::class . ':updatePasswordByEmail');

    // 用户信息
    $this->get(   '/users',                                           User::class . ':getList');
    $this->delete('/users',                                           User::class . ':disableMultiple');
    $this->get(   '/users/{user_id:\d+}',                             User::class . ':getOne');
    $this->patch( '/users/{user_id:\d+}',                             User::class . ':updateOne');
    $this->delete('/users/{user_id:\d+}',                             User::class . ':disableOne');
    $this->get(   '/user',                                            User::class . ':getMe');
    $this->patch( '/user',                                            User::class . ':updateMe');

    // 用户头像
    $this->delete('/users/{user_id:\d+}/avatar',                      User::class . ':deleteAvatar');
    $this->post(  '/user/avatar',                                     User::class . ':uploadMyAvatar');
    $this->delete('/user/avatar',                                     User::class . ':deleteMyAvatar');

    // 用户封面
    $this->delete('/users/{user_id:\d+}/cover',                       User::class . ':deleteCover');
    $this->post(  '/user/cover',                                      User::class . ':uploadMyCover');
    $this->delete('/user/cover',                                      User::class . ':deleteMyCover');

    // 用户关注
    $this->get(   '/users/{user_id:\d+}/followers',                   User::class . ':getFollowers');
    $this->post(  '/users/{user_id:\d+}/followers',                   User::class . ':addFollow');
    $this->delete('/users/{user_id:\d+}/followers',                   User::class . ':deleteFollow');
    $this->get(   '/users/{user_id:\d+}/followees',                   User::class . ':getFollowees');
    $this->get(   '/user/followers',                                  User::class . ':getMyFollowers');
    $this->get(   '/user/followees',                                  User::class . ':getMyFollowees');

    // 禁用的用户
    $this->get(   '/trash/users',                                     User::class . ':getDisabledList');
    $this->post(  '/trash/users',                                     User::class . ':enableMultiple');
    $this->post(  '/trash/users/{user_id:\d+}',                       User::class . ':enableOne');

    // 话题信息
    $this->get(   '/topics',                                          Topic::class . ':getList');
    $this->post(  '/topics',                                          Topic::class . ':create');
    $this->delete('/topics',                                          Topic::class . ':deleteMultiple');
    $this->get(   '/topics/{topic_id:\d+}',                           Topic::class . ':getOne');
    $this->post(  '/topics/{topic_id:\d+}',                           Topic::class . ':updateOne'); // formData 数据只能通过 post 请求提交，所以这里不用 patch 请求
    $this->delete('/topics/{topic_id:\d+}',                           Topic::class . ':deleteOne');

    // 话题关注
    $this->get(   '/users/{user_id:\d+}/following_topics',            Topic::class . ':getFollowing');
    $this->get(   '/user/following_topics',                           Topic::class . ':getMyFollowing');
    $this->get(   '/topics/{topic_id:\d+}/followers',                 Topic::class . ':getFollowers');
    $this->post(  '/topics/{topic_id:\d+}/followers',                 Topic::class . ':addFollow');
    $this->delete('/topics/{topic_id:\d+}/followers',                 Topic::class . ':deleteFollow');

    // 话题回收站
    $this->get(   '/trash/topics',                                    Topic::class . ':getDeletedList');
    $this->post(  '/trash/topics',                                    Topic::class . ':restoreMultiple');
    $this->delete('/trash/topics',                                    Topic::class . ':destroyMultiple');
    $this->post(  '/trash/topics/{topic_id:\d+}',                     Topic::class . ':restoreOne');
    $this->delete('/trash/topics/{topic_id:\d+}',                     Topic::class . ':destroyOne');

    // 问题信息
    $this->get(   '/users/{user_id:\d+}/questions',                   Question::class . ':getListByUserId');
    $this->get(   '/user/questions',                                  Question::class . ':getMyList');
    $this->get(   '/topics/{topic_id:\d+}/questions',                 Question::class . ':getListByTopicId');
    $this->get(   '/questions',                                       Question::class . ':getList');
    $this->post(  '/questions',                                       Question::class . ':create');
    $this->delete('/questions',                                       Question::class . ':deleteMultiple');
    $this->get(   '/questions/{question_id:\d+}',                     Question::class . ':getOne');
    $this->patch( '/questions/{question_id:\d+}',                     Question::class . ':updateOne');
    $this->delete('/questions/{question_id:\d+}',                     Question::class . ':deleteOne');

    // 问题评论
    $this->get(   '/questions/{question_id:\d+}/comments',            Question::class . ':getComments');
    $this->post(  '/questions/{question_id:\d+}/comments',            Question::class . ':addComment');

    // 问题投票
    $this->get(   '/questions/{question_id:\d+}/voters',              Question::class . ':getVoters');
    $this->post(  '/questions/{question_id:\d+}/voters',              Question::class . ':addVote');
    $this->delete('/questions/{question_id:\d+}/voters',              Question::class . ':deleteVote');

    // 问题关注
    $this->get(   '/users/{user_id:\d+}/following_questions',         Question::class . ':getFollowing');
    $this->get(   '/user/following_questions',                        Question::class . ':getMyFollowing');
    $this->get(   '/questions/{question_id:\d+}/followers',           Question::class . ':getFollowers');
    $this->post(  '/questions/{question_id:\d+}/followers',           Question::class . ':addFollow');
    $this->delete('/questions/{question_id:\d+}/followers',           Question::class . ':deleteFollow');

    // 问题回收站
    $this->get(   '/trash/questions',                                 Question::class . ':getDeletedList');
    $this->post(  '/trash/questions',                                 Question::class . ':restoreMultiple');
    $this->delete('/trash/questions',                                 Question::class . ':destroyMultiple');
    $this->post(  '/trash/questions/{question_id:\d+}',               Question::class . ':restoreOne');
    $this->delete('/trash/questions/{question_id:\d+}',               Question::class . ':destroyOne');

    // 回答信息
    $this->get(   '/users/{user_id:\d+}/answers',                     Answer::class . ':getListByUserId');
    $this->get(   '/user/answers',                                    Answer::class . ':getMyList');
    $this->get(   '/questions/{question_id:\d+}/answers',             Answer::class . ':getListByQuestionId');
    $this->post(  '/questions/{question_id:\d+}/answers',             Answer::class . ':create');
    $this->get(   '/answers',                                         Answer::class . ':getList');
    $this->delete('/answers',                                         Answer::class . ':deleteMultiple');
    $this->get(   '/answers/{answer_id:\d+}',                         Answer::class . ':getOne');
    $this->patch( '/answers/{answer_id:\d+}',                         Answer::class . ':updateOne');
    $this->delete('/answers/{answer_id:\d+}',                         Answer::class . ':deleteOne');

    // 回答评论
    $this->get(   '/answers/{answer_id:\d+}/comments',                Answer::class . ':getComments');
    $this->post(  '/answers/{answer_id:\d+}/comments',                Answer::class . ':addComment');

    // 回答投票
    $this->get(   '/answers/{answer_id:\d+}/voters',                  Answer::class . ':getVoters');
    $this->post(  '/answers/{answer_id:\d+}/voters',                  Answer::class . ':addVote');
    $this->delete('/answers/{answer_id:\d+}/voters',                  Answer::class . ':deleteVote');

    // 回答回收站
    $this->get(   '/trash/answers',                                   Answer::class . ':getDeletedList');
    $this->post(  '/trash/answers',                                   Answer::class . ':restoreMultiple');
    $this->delete('/trash/answers',                                   Answer::class . ':destroyMultiple');
    $this->post(  '/trash/answers/{answer_id:\d+}',                   Answer::class . ':restoreOne');
    $this->delete('/trash/answers/{answer_id:\d+}',                   Answer::class . ':destroyOne');

    // 文章信息
    $this->get(   '/users/{user_id:\d+}/articles',                    Article::class . ':getListByUserId');
    $this->get(   '/user/articles',                                   Article::class . ':getMyList');
    $this->get(   '/topics/{topic_id}/articles',                      Article::class . ':getListByTopicId');
    $this->get(   '/articles',                                        Article::class . ':getList');
    $this->post(  '/articles',                                        Article::class . ':create');
    $this->delete('/articles',                                        Article::class . ':deleteMultiple');
    $this->get(   '/articles/{article_id:\d+}',                       Article::class . ':getOne');
    $this->patch( '/articles/{article_id:\d+}',                       Article::class . ':updateOne');
    $this->delete('/articles/{article_id:\d+}',                       Article::class . ':deleteOne');

    // 文章评论
    $this->get(   '/articles/{article_id:\d+}/comments',              Article::class . ':getComments');
    $this->post(  '/articles/{article_id:\d+}/comments',              Article::class . ':addComment');

    // 文章投票
    $this->get(   '/articles/{article_id:\d+}/voters',                Article::class . ':getVoters');
    $this->post(  '/articles/{article_id:\d+}/voters',                Article::class . ':addVote');
    $this->delete('/articles/{article_id:\d+}/voters',                Article::class . ':deleteVote');

    // 文章关注
    $this->get(   '/users/{user_id:\d+}/following_articles',          Article::class . ':getFollowing');
    $this->get(   '/user/following_articles',                         Article::class . ':getMyFollowing');
    $this->get(   '/articles/{article_id:\d+}/followers',             Article::class . ':getFollowers');
    $this->post(  '/articles/{article_id:\d+}/followers',             Article::class . ':addFollow');
    $this->delete('/articles/{article_id:\d+}/followers',             Article::class . ':deleteFollow');

    // 文章回收站
    $this->get(   '/trash/articles',                                  Article::class . ':getDeletedList');
    $this->post(  '/trash/articles',                                  Article::class . ':restoreMultiple');
    $this->delete('/trash/articles',                                  Article::class . ':destroyMultiple');
    $this->post(  '/trash/articles/{article_id:\d+}',                 Article::class . ':restoreOne');
    $this->delete('/trash/articles/{article_id:\d+}',                 Article::class . ':destroyOne');

    // 评论
    $this->get(   '/users/{user_id:\d+}/comments',                    Comment::class . ':getListByUserId');
    $this->get(   '/user/comments',                                   Comment::class . ':getMyList');
    $this->get(   '/comments',                                        Comment::class . ':getList');
    $this->delete('/comments',                                        Comment::class . ':deleteMultiple');
    $this->get(   '/comments/{comment_id:\d+}',                       Comment::class . ':getOne');
    $this->patch( '/comments/{comment_id:\d+}',                       Comment::class . ':updateOne');
    $this->delete('/comments/{comment_id:\d+}',                       Comment::class . ':deleteOne');

    // 评论投票
    $this->get(   '/comments/{comment_id:\d+}/voters',                Comment::class . ':getVoters');
    $this->post(  '/comments/{comment_id:\d+}/voters',                Comment::class . ':addVote');
    $this->delete('/comments/{comment_id:\d+}/voters',                Comment::class . ':deleteVote');

    // 评论回收站
    $this->get(   '/trash/comments',                                  Comment::class . ':getDeletedList');
    $this->post(  '/trash/comments',                                  Comment::class . ':restoreMultiple');
    $this->delete('/trash/comments',                                  Comment::class . ':destroyMultiple');
    $this->post(  '/trash/comments/{comment_id:\d+}',                 Comment::class . ':restoreOne');
    $this->delete('/trash/comments/{comment_id:\d+}',                 Comment::class . ':destroyOne');

    // 举报
    $this->get(   '/reports',                                         Report::class . ':getList');
    $this->delete('/reports',                                         Report::class . ':deleteMultiple');
    $this->get(   '/reports/{reportable_type}/{reportable_id:\d+}',   Report::class . ':getDetailList');
    $this->post(  '/reports/{reportable_type}/{reportable_id:\d+}',   Report::class . ':create');
    $this->delete('/reports/{reportable_type}/{reportable_id:\d+}',   Report::class . ':deleteOne');

    // 图形验证码
    $this->post(  '/captchas',                                        Captcha::class . ':create');

    // 邮件
    $this->post(  '/emails',                                          Email::class . ':send');

    // 图片
    $this->get(   '/images',                                          Image::class . ':getList');
    $this->post(  '/images',                                          Image::class . ':upload');
    $this->delete('/images',                                          Image::class . ':deleteMultiple');
    $this->get(   '/images/{hash}',                                   Image::class . ':getOne');
    $this->patch( '/images/{hash}',                                   Image::class . ':updateOne');
    $this->delete('/images/{hash}',                                   Image::class . ':deleteOne');
})->add(new EnableCrossRequest());

// rss
$app->group('/rss', function () {
    $this->get('/questions',                           Rss::class . ':getQuestions');
    $this->get('/articles',                            Rss::class . ':getArticles');
    $this->get('/users/{user_id:\d+}/questions',       Rss::class . ':getQuestionsByUserId');
    $this->get('/users/{user_id:\d+}/articles',        Rss::class . ':getArticlesByUserId');
    $this->get('/topics/{topic_id:\d+}/questions',     Rss::class . ':getQuestionsByTopicId');
    $this->get('/topics/{topic_id:\d+}/articles',      Rss::class . ':getArticlesByTopicId');
    $this->get('/users/{user_id:\d+}/answers',         Rss::class . ':getAnswersByUserId');
    $this->get('/questions/{question_id:\d+}/answers', Rss::class . ':getAnswersByQuestionId');
});

// admin
$app->group('/admin', function () {
    $this->get('',                   Admin::class . ':pageIndex');
    $this->get('/{name}',            Admin::class . ':pageIndex');
    $this->get('/{name}/{sub_name}', Admin::class . ':pageIndex');
});
