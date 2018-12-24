<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;

$container = $app->getContainer();

/**
 * whoops：漂亮的错误界面
 *
 * @link https://github.com/filp/whoops
 */
if (APP_DEBUG) {
    /** @var \Slim\Http\Request $request */
    $request = $container->get('request');

    $accept = $request->getHeaderLine('accept');

    if (strpos($accept, 'application/json') > -1) {
        $handler = new \Whoops\Handler\JsonResponseHandler();
        $handler->setJsonApi(true)->addTraceToOutput(true);
    } else {
        $handler = new \Whoops\Handler\PrettyPageHandler();
    }

    $whoops = new \Whoops\Run;
    $whoops->pushHandler($handler)->register();
}

/**
 * 展开控制器方法的第三个参数
 *
 * @link https://www.slimframework.com/docs/v3/objects/router.html#route-strategies
 *
 * @return \Slim\Handlers\Strategies\RequestResponseArgs
 */
$container['foundHandler'] = function () {
    return new \Slim\Handlers\Strategies\RequestResponseArgs();
};

/**
 * 404 错误处理
 *
 * @param  ContainerInterface $container
 * @return \App\Handlers\NotFound
 */
$container['notFoundHandler'] = function (ContainerInterface $container) {
    return new \App\Handlers\NotFound($container);
};

/**
 * 405 错误处理
 *
 * @param  ContainerInterface $container
 * @return \App\Handlers\NotAllowed
 */
$container['notAllowedHandler'] = function (ContainerInterface $container) {
    return new \App\Handlers\NotAllowed($container);
};

/**
 * PHP错误处理
 *
 * @param  ContainerInterface $container
 * @return \App\Handlers\PhpError
 */
$container['phpErrorHandler'] = function (ContainerInterface $container) {
    return new \App\Handlers\PhpError($container);
};

/**
 * 异常处理
 *
 * @param  ContainerInterface $container
 * @return \App\Handlers\Error
 */
$container['errorHandler'] = function (ContainerInterface $container) {
    return new \App\Handlers\Error($container);
};

/**
 * 数据库操作工具
 *
 * @link https://medoo.lvtao.net/1.2/doc.php
 *
 * @param  ContainerInterface $container
 * @return \Medoo\Medoo
 */
$container[\Medoo\Medoo::class] = function (ContainerInterface $container) {
    $config = $container->get('settings')['database'];

    return new \Medoo\Medoo([
        'database_type' => $config['driver'],
        'server'        => $config['host'],
        'database_name' => $config['database'],
        'username'      => $config['username'],
        'password'      => $config['password'],
        'charset'       => $config['charset'],
        'port'          => $config['port'],
        'prefix'        => $config['prefix'],
        'logging'       => APP_DEBUG,
        'option'        => [
            PDO::ATTR_CASE               => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS       => PDO::NULL_NATURAL,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ],
    ]);
};

/**
 * 应用模块
 */

$modules = [
    \App\Library\Cache::class,
    \App\Library\FileCache::class,
    \App\Library\KvCache::class,
    \App\Library\Logger::class,
    \App\Library\Storage::class,
    \App\Library\View::class,

    \App\Model\AnswerModel::class,
    \App\Model\ArticleModel::class,
    \App\Model\CommentModel::class,
    \App\Model\FollowModel::class,
    \App\Model\ImageModel::class,
    \App\Model\InboxModel::class,
    \App\Model\NotificationModel::class,
    \App\Model\OptionModel::class,
    \App\Model\QuestionModel::class,
    \App\Model\ReportModel::class,
    \App\Model\TokenModel::class,
    \App\Model\TopicableModel::class,
    \App\Model\TopicModel::class,
    \App\Model\UserModel::class,
    \App\Model\VoteModel::class,

    \App\Service\AnswerService::class,
    \App\Service\ArticleService::class,
    \App\Service\CaptchaService::class,
    \App\Service\CommentService::class,
    \App\Service\EmailService::class,
    \App\Service\FollowService::class,
    \App\Service\ImageService::class,
    \App\Service\InboxService::class,
    \App\Service\NotificationService::class,
    \App\Service\OptionService::class,
    \App\Service\QuestionService::class,
    \App\Service\ReportService::class,
    \App\Service\RoleService::class,
    \App\Service\ThrottleService::class,
    \App\Service\TokenService::class,
    \App\Service\TopicService::class,
    \App\Service\UserAvatarService::class,
    \App\Service\UserCoverService::class,
    \App\Service\UserLoginService::class,
    \App\Service\UserPasswordResetService::class,
    \App\Service\UserRegisterService::class,
    \App\Service\UserService::class,
    \App\Service\VoteService::class,
];

foreach ($modules as $class) {
    $container[$class] = function (ContainerInterface $container) use ($class) {
        return new $class($container);
    };
}
