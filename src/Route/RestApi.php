<?php

declare(strict_types=1);

namespace MDClub\Route;

use MDClub\Controller\RestApi\Answer;
use MDClub\Controller\RestApi\Article;
use MDClub\Controller\RestApi\Captcha;
use MDClub\Controller\RestApi\Comment;
use MDClub\Controller\RestApi\Email;
use MDClub\Controller\RestApi\Image;
use MDClub\Controller\RestApi\Notification;
use MDClub\Controller\RestApi\Option;
use MDClub\Controller\RestApi\Question;
use MDClub\Controller\RestApi\Report;
use MDClub\Controller\RestApi\Stats;
use MDClub\Controller\RestApi\Token;
use MDClub\Controller\RestApi\Topic;
use MDClub\Controller\RestApi\User;
use MDClub\Initializer\App;
use MDClub\Middleware\EnableCrossRequest;
use MDClub\Middleware\JsonBodyParser;
use MDClub\Middleware\NeedInstalled;
use MDClub\Middleware\NeedLogin;
use MDClub\Middleware\NeedManager;
use MDClub\Middleware\Transformer\Answer as TransformerForAnswer;
use MDClub\Middleware\Transformer\Article as TransformerForArticle;
use MDClub\Middleware\Transformer\Comment as TransformerForComment;
use MDClub\Middleware\Transformer\Image as TransformerForImage;
use MDClub\Middleware\Transformer\Notification as TransformerForNotification;
use MDClub\Middleware\Transformer\Question as TransformerForQuestion;
use MDClub\Middleware\Transformer\Report as TransformerForReport;
use MDClub\Middleware\Transformer\ReportReason as TransformerForReportReason;
use MDClub\Middleware\Transformer\Topic as TransformerForTopic;
use MDClub\Middleware\Transformer\User as TransformerForUser;
use Psr\Http\Message\ResponseInterface;
use Slim\Routing\RouteCollectorProxy;

/**
 * RestApi 路由
 */
class RestApi
{
    public function __construct()
    {
        $slim = App::$slim;
        $route = $this;

        $slim
            ->group('/api', function (RouteCollectorProxy $group) use ($route) {
                $group->options('/{routes:.+}', function () {
                    return App::$container->get(ResponseInterface::class);
                });

                $route->stats($group);
                $route->token($group);
                $route->option($group);
                $route->user($group);
                $route->topic($group);
                $route->question($group);
                $route->answer($group);
                $route->article($group);
                $route->comment($group);
                $route->report($group);
                $route->inbox($group);
                $route->notification($group);
                $route->captcha($group);
                $route->email($group);
                $route->image($group);
            })
            ->add(EnableCrossRequest::class)
            ->add(JsonBodyParser::class)
            ->add(NeedInstalled::class);
    }

    /**
     * 从控制器名中提取名称
     *
     * @param  string $controllerPath
     * @return string
     */
    private function getNameFromControllerPath(string $controllerPath): string
    {
        $arr = explode('\\', $controllerPath);

        return strtolower(end($arr));
    }

    /**
     * 根据名称获取转换器路径
     *
     * @param  string $name
     * @return string
     */
    private function getTransformerPathFromName(string $name): string
    {
        $map = [
            'answer' => TransformerForAnswer::class,
            'article' => TransformerForArticle::class,
            'comment' => TransformerForComment::class,
            'image' => TransformerForImage::class,
            'question' => TransformerForQuestion::class,
            'report' => TransformerForReport::class,
            'reportReason' => TransformerForReportReason::class,
            'topic' => TransformerForTopic::class,
            'user' => TransformerForUser::class,
        ];

        return $map[$name];
    }

    /**
     * 添加可评论对象的路由
     *
     * @param RouteCollectorProxy $group
     * @param string              $controllerPath
     */
    protected function addCommentableRoute(RouteCollectorProxy $group, string $controllerPath): void
    {
        $name = $this->getNameFromControllerPath($controllerPath);
        $pattern = "/{$name}s/{{$name}_id:\d+}/comments";

        $group
            ->get($pattern, "{$controllerPath}:getComments")
            ->add(TransformerForComment::class);

        $group
            ->post($pattern, "{$controllerPath}:createComment")
            ->add(TransformerForComment::class)
            ->add(NeedLogin::class);
    }

    /**
     * 添加可删除对象路由
     *
     * @param RouteCollectorProxy $group
     * @param string              $controllerPath
     */
    protected function addDeletableRoute(RouteCollectorProxy $group, string $controllerPath): void
    {
        $name = $this->getNameFromControllerPath($controllerPath);
        $transformerPath = $this->getTransformerPathFromName($name);

        $group
            ->delete("/{$name}s/{{$name}_id:\d+}", "{$controllerPath}:delete")
            ->add($name === 'topic' ? NeedManager::class : NeedLogin::class);

        $group
            ->delete("/{$name}s/{{$name}_ids}", "{$controllerPath}:deleteMultiple")
            ->add(NeedManager::class);

        $group
            ->post("/{$name}s/{{$name}_id:\d+}/trash", "{$controllerPath}:trash")
            ->add($transformerPath)
            ->add(NeedManager::class);

        $group
            ->post("/{$name}s/{{$name}_ids}/trash", "{$controllerPath}:trashMultiple")
            ->add($transformerPath)
            ->add(NeedManager::class);

        $group
            ->post("/{$name}s/{{$name}_id:\d+}/untrash", "{$controllerPath}:untrash")
            ->add($transformerPath)
            ->add(NeedManager::class);

        $group
            ->post("/{$name}s/{{$name}_ids}/untrash", "{$controllerPath}:untrashMultiple")
            ->add($transformerPath)
            ->add(NeedManager::class);
    }

    /**
     * 添加可关注对象的路由
     *
     * @param RouteCollectorProxy $group
     * @param string              $controllerPath
     */
    protected function addFollowableRoute(RouteCollectorProxy $group, string $controllerPath): void
    {
        $name = $this->getNameFromControllerPath($controllerPath);
        $pattern = "/{$name}s/{{$name}_id:\d+}/followers";

        $group
            ->get($pattern, "{$controllerPath}:getFollowers")
            ->add(TransformerForUser::class);

        $group
            ->post($pattern, "{$controllerPath}:addFollow")
            ->add(NeedLogin::class);

        $group
            ->delete($pattern, "{$controllerPath}:deleteFollow")
            ->add(NeedLogin::class);
    }

    /**
     * 添加可获取对象路由
     *
     * @param RouteCollectorProxy $group
     * @param string              $controllerPath
     */
    protected function addGetableRoute(RouteCollectorProxy $group, string $controllerPath): void
    {
        $name = $this->getNameFromControllerPath($controllerPath);
        $transformerPath = $this->getTransformerPathFromName($name);

        $listGroup = $group
            ->get("/{$name}s", "{$controllerPath}:getList")
            ->add($transformerPath);

        if ($name === 'answer' || $name === 'comment') {
            $listGroup->add(NeedManager::class);
        }

        $group
            ->get("/{$name}s/{{$name}_id:\d+}", "{$controllerPath}:get")
            ->add($transformerPath);
    }

    /**
     * 添加可投票对象路由
     *
     * @param RouteCollectorProxy $group
     * @param string              $controllerPath
     */
    protected function addVotableRoute(RouteCollectorProxy $group, string $controllerPath): void
    {
        $name = $this->getNameFromControllerPath($controllerPath);
        $pattern = "/{$name}s/{{$name}_id:\d+}/voters";

        $group
            ->get($pattern, "{$controllerPath}:getVoters")
            ->add(TransformerForUser::class);

        $group
            ->post($pattern, "{$controllerPath}:addVote")
            ->add(NeedLogin::class);

        $group
            ->delete($pattern, "{$controllerPath}:deleteVote")
            ->add(NeedLogin::class);
    }

    /**
     * 统计数据
     *
     * @param RouteCollectorProxy $group
     */
    protected function stats(RouteCollectorProxy $group): void
    {
        $group->get('/stats', Stats::class . ':get')
            ->add(NeedManager::class);
    }

    /**
     * 身份验证
     *
     * @param  RouteCollectorProxy $group
     */
    protected function token(RouteCollectorProxy $group): void
    {
        $group->post('/tokens', Token::class . ':create');
    }

    /**
     * 系统设置
     *
     * @param  RouteCollectorProxy $group
     */
    protected function option(RouteCollectorProxy $group): void
    {
        $group
            ->get('/options', Option::class . ':get');

        $group
            ->patch('/options', Option::class . ':update')
            ->add(NeedManager::class);
    }

    /**
     * 用户
     *
     * @param  RouteCollectorProxy $group
     */
    protected function user(RouteCollectorProxy $group): void
    {
        $this->addFollowableRoute($group, User::class);
        $this->addGetableRoute($group, User::class);

        $group
            ->post('/users', User::class . ':register')
            ->add(TransformerForUser::class);

        $group
            ->patch('/users/{user_id:\d+}', User::class . ':update')
            ->add(TransformerForUser::class)
            ->add(NeedManager::class);

        $group
            ->delete('/users/{user_id:\d+}/avatar', User::class . ':deleteAvatar')
            ->add(NeedManager::class);

        $group
            ->delete('/users/{user_id:\d+}/cover', User::class . ':deleteCover')
            ->add(NeedManager::class);

        $group
            ->get('/users/{user_id:\d+}/followees', User::class . ':getFollowees')
            ->add(TransformerForUser::class);

        $group
            ->get('/users/{user_id:\d+}/following_questions', User::class . ':getFollowingQuestions')
            ->add(TransformerForQuestion::class);

        $group
            ->get('/users/{user_id:\d+}/following_articles', User::class . ':getFollowingArticles')
            ->add(TransformerForArticle::class);

        $group
            ->get('/users/{user_id:\d+}/following_topics', User::class . ':getFollowingTopics')
            ->add(TransformerForTopic::class);

        $group
            ->get('/users/{user_id:\d+}/questions', User::class . ':getQuestions')
            ->add(TransformerForQuestion::class);

        $group
            ->get('/users/{user_id:\d+}/answers', User::class . ':getAnswers')
            ->add(TransformerForAnswer::class);

        $group
            ->get('/users/{user_id:\d+}/articles', User::class . ':getArticles')
            ->add(TransformerForArticle::class);

        $group
            ->get('/users/{user_id:\d+}/comments', User::class . ':getComments')
            ->add(TransformerForComment::class);

        $group
            ->get('/user', User::class . ':getMine')
            ->add(TransformerForUser::class)
            ->add(NeedLogin::class);

        $group
            ->patch('/user', User::class . ':updateMine')
            ->add(TransformerForUser::class)
            ->add(NeedLogin::class);

        $group
            ->post('/user/avatar', User::class . ':uploadMyAvatar')
            ->add(NeedLogin::class);

        $group
            ->delete('/user/avatar', User::class . ':deleteMyAvatar')
            ->add(NeedLogin::class);

        $group
            ->post('/user/cover', User::class . ':uploadMyCover')
            ->add(NeedLogin::class);

        $group
            ->delete('/user/cover', User::class . ':deleteMyCover')
            ->add(NeedLogin::class);

        $group
            ->post('/user/register/email', User::class . ':sendRegisterEmail');

        $group
            ->post('/user/password/email', User::class . ':sendPasswordResetEmail');

        $group
            ->put('/user/password', User::class . ':updatePassword');

        $group
            ->get('/user/followers', User::class . ':getMyFollowers')
            ->add(TransformerForUser::class)
            ->add(NeedLogin::class);

        $group
            ->get('/user/followees', User::class . ':getMyFollowees')
            ->add(TransformerForUser::class)
            ->add(NeedLogin::class);

        $group
            ->get('/user/following_questions', User::class . ':getMyFollowingQuestions')
            ->add(TransformerForQuestion::class)
            ->add(NeedLogin::class);

        $group
            ->get('/user/following_articles', User::class . ':getMyFollowingArticles')
            ->add(TransformerForArticle::class)
            ->add(NeedLogin::class);

        $group
            ->get('/user/following_topics', User::class . ':getMyFollowingTopics')
            ->add(TransformerForTopic::class)
            ->add(NeedLogin::class);

        $group
            ->get('/user/questions', User::class . ':getMyQuestions')
            ->add(TransformerForQuestion::class)
            ->add(NeedLogin::class);

        $group
            ->get('/user/answers', User::class . ':getMyAnswers')
            ->add(TransformerForAnswer::class)
            ->add(NeedLogin::class);

        $group
            ->get('/user/articles', User::class . ':getMyArticles')
            ->add(TransformerForArticle::class)
            ->add(NeedLogin::class);

        $group
            ->get('/user/comments', User::class . ':getMyComments')
            ->add(TransformerForComment::class)
            ->add(NeedLogin::class);

        $group
            ->post('/users/{user_id:\d+}/disable', User::class . ':disable')
            ->add(TransformerForUser::class)
            ->add(NeedManager::class);

        $group
            ->post('/users/{user_ids}/disable', User::class . ':disableMultiple')
            ->add(TransformerForUser::class)
            ->add(NeedManager::class);

        $group
            ->post('/users/{user_id:\d+}/enable', User::class . ':enable')
            ->add(TransformerForUser::class)
            ->add(NeedManager::class);

        $group
            ->post('/users/{user_ids}/enable', User::class . ':enableMultiple')
            ->add(TransformerForUser::class)
            ->add(NeedManager::class);
    }

    /**
     * 话题
     *
     * @param  RouteCollectorProxy $group
     */
    protected function topic(RouteCollectorProxy $group): void
    {
        $this->addDeletableRoute($group, Topic::class);
        $this->addFollowableRoute($group, Topic::class);
        $this->addGetableRoute($group, Topic::class);

        $group
            ->post('/topics', Topic::class . ':create')
            ->add(TransformerForTopic::class)
            ->add(NeedManager::class);

        $group
            ->post('/topics/{topic_id:\d+}', Topic::class . ':update')
            ->add(TransformerForTopic::class)
            ->add(NeedManager::class);

        $group
            ->get('/topics/{topic_id:\d+}/questions', Topic::class . ':getQuestions')
            ->add(TransformerForQuestion::class);

        $group
            ->get('/topics/{topic_id:\d+}/articles', Topic::class . ':getArticles')
            ->add(TransformerForArticle::class);
    }

    /**
     * 提问
     *
     * @param  RouteCollectorProxy $group
     */
    protected function question(RouteCollectorProxy $group): void
    {
        $this->addCommentableRoute($group, Question::class);
        $this->addDeletableRoute($group, Question::class);
        $this->addFollowableRoute($group, Question::class);
        $this->addGetableRoute($group, Question::class);
        $this->addVotableRoute($group, Question::class);

        $group
            ->post('/questions', Question::class . ':create')
            ->add(TransformerForQuestion::class)
            ->add(NeedLogin::class);

        $group
            ->patch('/questions/{question_id:\d+}', Question::class . ':update')
            ->add(TransformerForQuestion::class)
            ->add(NeedLogin::class);

        $group
            ->get('/questions/{question_id:\d+}/answers', Question::class . ':getAnswers')
            ->add(TransformerForAnswer::class);

        $group
            ->post('/questions/{question_id:\d+}/answers', Question::class . ':createAnswer')
            ->add(TransformerForAnswer::class)
            ->add(NeedLogin::class);
    }

    /**
     * 回答
     *
     * @param  RouteCollectorProxy $group
     */
    protected function answer(RouteCollectorProxy $group): void
    {
        $this->addCommentableRoute($group, Answer::class);
        $this->addDeletableRoute($group, Answer::class);
        $this->addGetableRoute($group, Answer::class);
        $this->addVotableRoute($group, Answer::class);

        $group
            ->patch('/answers/{answer_id:\d+}', Answer::class . ':update')
            ->add(TransformerForAnswer::class)
            ->add(NeedLogin::class);
    }

    /**
     * 文章
     *
     * @param  RouteCollectorProxy $group
     */
    protected function article(RouteCollectorProxy $group): void
    {
        $this->addCommentableRoute($group, Article::class);
        $this->addDeletableRoute($group, Article::class);
        $this->addFollowableRoute($group, Article::class);
        $this->addGetableRoute($group, Article::class);
        $this->addVotableRoute($group, Article::class);

        $group
            ->post('/articles', Article::class . ':create')
            ->add(TransformerForArticle::class)
            ->add(NeedLogin::class);

        $group
            ->patch('/articles/{article_id:\d+}', Article::class . ':update')
            ->add(TransformerForArticle::class)
            ->add(NeedLogin::class);
    }

    /**
     * 评论
     *
     * @param  RouteCollectorProxy $group
     */
    protected function comment(RouteCollectorProxy $group): void
    {
        $this->addDeletableRoute($group, Comment::class);
        $this->addGetableRoute($group, Comment::class);
        $this->addVotableRoute($group, Comment::class);

        $group
            ->patch('/comments/{comment_id:\d+}', Comment::class . ':update')
            ->add(TransformerForComment::class)
            ->add(NeedLogin::class);

        $group
            ->post('/comments/{comment_id:\d+}/replies', Comment::class . ':createComment')
            ->add(TransformerForComment::class)
            ->add(NeedLogin::class);

        $group
            ->get('/comments/{comment_id:\d+}/replies', Comment::class . ':getComments')
            ->add(TransformerForComment::class);
    }

    /**
     * 举报
     *
     * @param  RouteCollectorProxy $group
     */
    protected function report(RouteCollectorProxy $group): void
    {
        $group
            ->get('/reports', Report::class . ':getList')
            ->add(TransformerForReport::class)
            ->add(NeedManager::class);

        $group
            ->delete('/reports/{report_targets:\S+,+\S+}', Report::class . ':deleteMultiple')
            ->add(NeedManager::class);

        $group
            ->get('/reports/{reportable_type}:{reportable_id:\d+}', Report::class . ':getReasons')
            ->add(TransformerForReportReason::class)
            ->add(NeedManager::class);

        $group
            ->post('/reports/{reportable_type}:{reportable_id:\d+}', Report::class . ':create')
            ->add(TransformerForReportReason::class)
            ->add(NeedLogin::class);

        $group
            ->delete('/reports/{reportable_type}:{reportable_id:\d+}', Report::class . ':delete')
            ->add(NeedManager::class);
    }

    /**
     * 私信
     *
     * @param  RouteCollectorProxy $group
     */
    protected function inbox(RouteCollectorProxy $group): void
    {
    }

    /**
     * 通知
     *
     * @param  RouteCollectorProxy $group
     */
    protected function notification(RouteCollectorProxy $group): void
    {
        $group
            ->get('/notifications/count', Notification::class . ':getCount')
            ->add(NeedLogin::class);

        $group
            ->post('/notifications/read', Notification::class . ':readAll')
            ->add(NeedLogin::class);

        $group
            ->get('/notifications', Notification::class . ':getList')
            ->add(TransformerForNotification::class)
            ->add(NeedLogin::class);

        $group
            ->delete('/notifications', Notification::class . ':deleteAll')
            ->add(NeedLogin::class);

        $group
            ->post('/notifications/{notification_ids}/read', Notification::class . ':readMultiple')
            ->add(TransformerForNotification::class)
            ->add(NeedLogin::class);

        $group
            ->post('/notifications/{notification_id:\d+}/read', Notification::class . ':read')
            ->add(TransformerForNotification::class)
            ->add(NeedLogin::class);

        $group
            ->delete('/notifications/{notification_id:\d+}', Notification::class . ':delete')
            ->add(NeedLogin::class);

        $group
            ->delete('/notifications/{notification_ids}', Notification::class . ':deleteMultiple')
            ->add(NeedLogin::class);
    }

    /**
     * 图形验证码
     *
     * @param  RouteCollectorProxy $group
     */
    protected function captcha(RouteCollectorProxy $group): void
    {
        $group
            ->post('/captchas', Captcha::class . ':create');
    }

    /**
     * 邮件
     *
     * @param  RouteCollectorProxy $group
     */
    protected function email(RouteCollectorProxy $group): void
    {
        $group
            ->post('/emails', Email::class . ':send')
            ->add(NeedManager::class);
    }

    /**
     * 图片
     *
     * @param  RouteCollectorProxy $group
     */
    protected function image(RouteCollectorProxy $group): void
    {
        $group
            ->get('/images', Image::class . ':getList')
            ->add(TransformerForImage::class)
            ->add(NeedManager::class);

        $group
            ->post('/images', Image::class . ':upload')
            ->add(TransformerForImage::class)
            ->add(NeedLogin::class);

        $group
            ->delete('/images/{keys:\S+(?=.*,)\S+}', Image::class . ':deleteMultiple')
            ->add(NeedManager::class);

        $group
            ->get('/images/{key}', Image::class . ':get')
            ->add(TransformerForImage::class);

        $group
            ->patch('/images/{key}', Image::class . ':update')
            ->add(TransformerForImage::class)
            ->add(NeedManager::class);

        $group
            ->delete('/images/{key}', Image::class . ':delete')
            ->add(NeedManager::class);
    }
}
