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

$handlers = [
    'foundHandler'      => \Slim\Handlers\Strategies\RequestResponseArgs::class,
    'notFoundHandler'   => \App\Handlers\NotFound::class,
    'notAllowedHandler' => \App\Handlers\NotAllowed::class,
    'phpErrorHandler'   => \App\Handlers\PhpError::class,
    'errorHandler'      => \App\Handlers\Error::class,
];

$modules = [
    \App\Library\Cache::class,
    \App\Library\Db::class,
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

foreach ($handlers as $name => $class) {
    $container[$name] = function (ContainerInterface $container) use ($class) {
        return new $class($container);
    };
}

foreach ($modules as $class) {
    $container[$class] = function (ContainerInterface $container) use ($class) {
        return new $class($container);
    };
}
