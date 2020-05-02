<?php

declare(strict_types=1);

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Option;
use MDClub\Facade\Service\AnswerService;
use MDClub\Facade\Service\ArticleService;
use MDClub\Facade\Service\CommentService;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Service\TopicService;
use MDClub\Facade\Service\UserService;
use MDClub\Facade\Transformer\AnswerTransformer;
use MDClub\Facade\Transformer\ArticleTransformer;
use MDClub\Facade\Transformer\CommentTransformer;
use MDClub\Facade\Transformer\QuestionTransformer;
use MDClub\Facade\Transformer\TopicTransformer;
use MDClub\Facade\Transformer\UserTransformer;
use MDClub\Helper\Url;
use MDClub\Initializer\App;
use MDClub\Initializer\Collection;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteContext;

/**
 * 创建集合
 *
 * @param  mixed      $value
 * @return Collection
 */
function collect($value = null)
{
    return new Collection($value);
}

// -----------------------------------------------------
// ------------- 下列函数仅在模板中使用 ----------------
// -----------------------------------------------------

// ----------------------------------------------------- 系统函数

/**
 * 获取路由对象
 *
 * @return RouteInterface
 */
function get_route(): RouteInterface
{
    $request = App::$container->get(ServerRequestInterface::class);
    $routeContext = RouteContext::fromRequest($request);

    return $routeContext->getRoute();
}

/**
 * 获取请求对象
 *
 * @param array $queryParams 附加的查询参数，若 url 中存在相同的参数，则会覆盖
 *
 * @return ServerRequestInterface
 */
function get_request(array $queryParams = []): ServerRequestInterface
{
    if (isset($queryParams['include']) && is_array($queryParams['include'])) {
        $queryParams['include'] = implode(',', $queryParams['include']);
    }

    /** @var ServerRequestInterface $request */
    $request = App::$container->get(ServerRequestInterface::class);

    $request = $request->withQueryParams(array_merge($request->getQueryParams(), $queryParams));

    App::$container->offsetSet(ServerRequestInterface::class, $request);

    return $request;
}

/**
 * 获取网站根目录网址（含域名）
 *
 * @return string
 */
function get_host_url(): string
{
    return Url::hostPath();
}

/**
 * 获取静态资源的访问路径
 *
 * @return string
 */
function get_static_url(): string
{
    return Url::staticPath();
}

/**
 * 获取当前主题的静态资源路径
 *
 * @return string
 */
function get_theme_static_url(): string
{
    return Url::themeStaticPath();
}

/**
 * 获取网站的根目录相对路径
 *
 * @return string
 */
function get_root_url(): string
{
    return Url::rootPath();
}

/**
 * 获取上传文件的访问路径
 *
 * @return string
 */
function get_storage_url(): string
{
    return Url::storagePath();
}

/**
 * 从路由生成链接
 *
 * @param string $name
 * @param array  $data
 * @param array  $queryParams
 *
 * @return string
 */
function generate_url_from_route(string $name, array $data = [], array $queryParams = []): string
{
    return Url::fromRoute($name, $data, $queryParams);
}

// ----------------------------------------------------- 系统配置函数

/**
 * 获取所有配置项
 *
 * 参见接口: GET /options
 *
 * @return array
 */
function get_options(): array
{
    return Option::getAll();
}

// ----------------------------------------------------- 用户函数

/**
 * 获取当前登录用户ID
 *
 * @return int|null
 */
function get_user_id()
{
    return Auth::userId();
}

/**
 * 获取用户信息
 *
 * 若 $userId 为 null，则获取当前登录用户的信息
 * 否则获取指定用户的信息
 *
 * @param int|null $userId
 * @param array    $queryParams include: `is_me`, `is_following`, `is_followed`
 *
 * @return array
 */
function get_user(int $userId = null, array $queryParams = []): array
{
    get_request($queryParams);

    if ($userId === null) {
        $userId = get_user_id();
    }

    $user = UserService::getOrFail($userId);
    $user = UserTransformer::transform($user);

    return $user;
}

/**
 * 获取用户列表
 *
 * 参见接口: GET /users
 *
 * @param array $queryParams
 *
 * @return array
 */
function get_users(array $queryParams = []): array
{
    get_request($queryParams);

    $users = UserService::getList();
    $users['data'] = UserTransformer::transform($users['data']);

    return $users;
}

/**
 * 获取指定用户关注的用户
 *
 * 参见接口: GET /users/{user_id}/followees
 *
 * @param int   $userId
 * @param array $queryParams
 *
 * @return array
 */
function get_followees(int $userId, array $queryParams = []): array
{
    get_request($queryParams);

    $users = UserService::getFollowing($userId);
    $users['data'] = UserTransformer::transform($users['data']);

    return $users;
}

/**
 * 获取指定用户的关注者
 *
 * 参见接口: GET /users/{user_id}/followers
 *
 * @param int   $userId
 * @param array $queryParams
 *
 * @return array
 */
function get_followers(int $userId, array $queryParams = []): array
{
    get_request($queryParams);

    $users = UserService::getFollowers($userId);
    $users['data'] = UserTransformer::transform($users['data']);

    return $users;
}

// ----------------------------------------------------- 话题函数

/**
 * 获取话题信息
 *
 * 参见接口: GET /topics/{topic_id}
 *
 * @param int   $topicId
 * @param array $queryParams include: `is_following`
 *
 * @return array
 */
function get_topic(int $topicId, array $queryParams = []): array
{
    get_request($queryParams);

    $topic = TopicService::getOrFail($topicId);
    $topic = TopicTransformer::transform($topic);

    return $topic;
}

/**
 * 获取话题列表
 *
 * 参见接口: GET /topics
 *
 * @param array $queryParams
 *
 * @return array
 */
function get_topics(array $queryParams = []): array
{
    get_request($queryParams);

    $topics = TopicService::getList();
    $topics['data'] = TopicTransformer::transform($topics['data']);

    return $topics;
}

/**
 * 获取指定用户关注的话题
 *
 * 参见接口: GET /users/{user_id}/following_topics
 *
 * @param int   $userId
 * @param array $queryParams
 *
 * @return array
 */
function get_following_topics(int $userId, array $queryParams = []): array
{
    get_request($queryParams);

    $topics = TopicService::getFollowing($userId);
    $topics['data'] = TopicTransformer::transform($topics['data']);

    return $topics;
}

// ----------------------------------------------------- 文章函数

/**
 * 获取文章信息
 *
 * 参见接口: GET /answers/{answer_id}
 *
 * @param int   $articleId
 * @param array $queryParams
 *
 * @return array
 */
function get_article(int $articleId, array $queryParams = []): array
{
    get_request($queryParams);

    $article = ArticleService::getOrFail($articleId);
    $article = ArticleTransformer::transform($article);

    return $article;
}

/**
 * 获取文章列表
 *
 * 参见接口: GET /answers
 *
 * @param array $queryParams
 *
 * @return array
 */
function get_articles(array $queryParams = []): array
{
    get_request($queryParams);

    $articles = ArticleService::getList();
    $articles['data'] = ArticleTransformer::transform($articles['data']);

    return $articles;
}

/**
 * 获取指定用户关注的文章列表
 *
 * 参见接口: GET /users/{user_id}/following_articles
 *
 * @param int   $userId
 * @param array $queryParams
 *
 * @return array
 */
function get_following_articles(int $userId, array $queryParams = []): array
{
    get_request($queryParams);

    $articles = ArticleService::getFollowing($userId);
    $articles['data'] = ArticleTransformer::transform($articles['data']);

    return $articles;
}

// ----------------------------------------------------- 提问函数

/**
 * 获取提问信息
 *
 * 参见接口: GET /questions/{question_id}
 *
 * @param int   $questionId
 * @param array $queryParams
 *
 * @return array
 */
function get_question(int $questionId, array $queryParams = []): array
{
    get_request($queryParams);

    $question = QuestionService::getOrFail($questionId);
    $question = QuestionTransformer::transform($question);

    return $question;
}

/**
 * 获取提问列表
 *
 * 参见接口: GET /questions
 *
 * @param array $queryParams
 *
 * @return array
 */
function get_questions(array $queryParams = []): array
{
    get_request($queryParams);

    $questions = QuestionService::getList();
    $questions['data'] = QuestionTransformer::transform($questions['data']);

    return $questions;
}

/**
 * 获取指定用户关注的提问列表
 *
 * 参见接口: GET /users/{user_id}/following_questions
 *
 * @param int   $userId
 * @param array $queryParams
 *
 * @return array
 */
function get_following_questions(int $userId, array $queryParams = []): array
{
    get_request($queryParams);

    $questions = QuestionService::getFollowing($userId);
    $questions['data'] = QuestionTransformer::transform($questions['data']);

    return $questions;
}

// ----------------------------------------------------- 回答函数

/**
 * 获取回答详情
 *
 * 参见接口：GET /answers/{answer_id}
 *
 * @param int   $answerId
 * @param array $queryParams
 *
 * @return array
 */
function get_answer(int $answerId, array $queryParams = []): array
{
    get_request($queryParams);

    $answer = AnswerService::getOrFail($answerId);
    $answer = AnswerTransformer::transform($answer);

    return $answer;
}

/**
 * 获取回答列表
 *
 * 参见接口: GET /answers
 *
 * @param array $queryParams
 *
 * @return array
 */
function get_answers(array $queryParams = []): array
{
    get_request($queryParams);

    $answers = AnswerService::getList();
    $answers['data'] = AnswerTransformer::transform($answers['data']);

    return $answers;
}

// ----------------------------------------------------- 评论函数

/**
 * 获取评论列表
 *
 * 参见接口: GET /comments
 *
 * @param array $queryParams
 *
 * @return array
 */
function get_comments(array $queryParams = []): array
{
    get_request($queryParams);

    $comments = CommentService::getList();
    $comments['data'] = CommentTransformer::transform($comments['data']);

    return $comments;
}
