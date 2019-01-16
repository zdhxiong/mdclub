<?php

declare(strict_types=1);

/**
 * @var \App\Interfaces\ContainerInterface $container
 */
$container = $app->getContainer();

/**
 * whoops：漂亮的错误界面
 *
 * @link https://github.com/filp/whoops
 */
if (APP_DEBUG) {
    $accept = $container->request->getHeaderLine('accept');

    if (strpos($accept, 'application/json') > -1) {
        $handler = new \Whoops\Handler\JsonResponseHandler();
        $handler->setJsonApi(true)->addTraceToOutput(true);
    } else {
        $handler = new \Whoops\Handler\PrettyPageHandler();
    }

    $whoops = new \Whoops\Run;
    $whoops->pushHandler($handler)->register();
}

$names = [
    /**
     * - settings
     * - environment
     * - request
     * - response
     * - router
     * - callableResolver
     */

    'foundHandler'             => \Slim\Handlers\Strategies\RequestResponseArgs::class,
    'notFoundHandler'          => \App\Handlers\NotFound::class,
    'notAllowedHandler'        => \App\Handlers\NotAllowed::class,
    'phpErrorHandler'          => \App\Handlers\PhpError::class,
    'errorHandler'             => \App\Handlers\Error::class,

    'cache'                    => \App\Library\Cache::class,
    'db'                       => \App\Library\Db::class,
    'logger'                   => \App\Library\Logger::class,
    'storage'                  => \App\Library\Storage::class,
    'view'                     => \App\Library\View::class,
    'redis'                    => \App\Library\Redis::class,

    'answerModel'              => \App\Model\Answer::class,
    'articleModel'             => \App\Model\Article::class,
    'commentModel'             => \App\Model\Comment::class,
    'followModel'              => \App\Model\Follow::class,
    'imageModel'               => \App\Model\Image::class,
    'inboxModel'               => \App\Model\Inbox::class,
    'notificationModel'        => \App\Model\Notification::class,
    'optionModel'              => \App\Model\Option::class,
    'questionModel'            => \App\Model\Question::class,
    'reportModel'              => \App\Model\Report::class,
    'tokenModel'               => \App\Model\Token::class,
    'topicableModel'           => \App\Model\Topicable::class,
    'topicModel'               => \App\Model\Topic::class,
    'userModel'                => \App\Model\User::class,
    'voteModel'                => \App\Model\Vote::class,

    'answerService'            => \App\Service\Answer::class,
    'articleService'           => \App\Service\Article::class,
    'captchaService'           => \App\Service\Captcha::class,
    'commentService'           => \App\Service\Comment::class,
    'emailService'             => \App\Service\Email::class,
    'followService'            => \App\Service\Follow::class,
    'imageService'             => \App\Service\Image::class,
    'inboxService'             => \App\Service\Inbox::class,
    'notificationService'      => \App\Service\Notification::class,
    'optionService'            => \App\Service\Option::class,
    'questionService'          => \App\Service\Question::class,
    'reportService'            => \App\Service\Report::class,
    'roleService'              => \App\Service\Role::class,
    'throttleService'          => \App\Service\Throttle::class,
    'tokenService'             => \App\Service\Token::class,
    'topicService'             => \App\Service\Topic::class,
    'userAvatarService'        => \App\Service\UserAvatar::class,
    'userCoverService'         => \App\Service\UserCover::class,
    'userLoginService'         => \App\Service\UserLogin::class,
    'userPasswordResetService' => \App\Service\UserPasswordReset::class,
    'userRegisterService'      => \App\Service\UserRegister::class,
    'userService'              => \App\Service\User::class,
    'voteService'              => \App\Service\Vote::class,
];

foreach ($names as $key => $name) {
    $container[$key] = function ($container) use ($name) {
        return new $name($container);
    };
}
