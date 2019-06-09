<?php

declare(strict_types=1);

/**
 * @var \Psr\Container\ContainerInterface $container
 */
$container = $app->getContainer();

/**
 * whoops：漂亮的错误界面
 *
 * @link https://github.com/filp/whoops
 */
if (APP_DEBUG) {
    $accept = $container->get('request')->getHeaderLine('accept');

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

    'httpCache'                => \Slim\HttpCache\CacheProvider::class,
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

    'answerCommentService'     => \App\Service\Answer\Comment::class,
    'answerDeleteService'      => \App\Service\Answer\Delete::class,
    'answerGetService'         => \App\Service\Answer\Get::class,
    'answerUpdateService'      => \App\Service\Answer\Update::class,
    'answerVoteService'        => \App\Service\Answer\Vote::class,
    'articleCommentService'    => \App\Service\Article\Comment::class,
    'articleDeleteService'     => \App\Service\Article\Delete::class,
    'articleFollowService'     => \App\Service\Article\Follow::class,
    'articleGetService'        => \App\Service\Article\Get::class,
    'articleUpdateService'     => \App\Service\Article\Update::class,
    'articleVoteService'       => \App\Service\Article\Vote::class,
    'commentDeleteService'     => \App\Service\Comment\Delete::class,
    'commentGetService'        => \App\Service\Comment\Get::class,
    'commentUpdateService'     => \App\Service\Comment\Update::class,
    'commentVoteService'       => \App\Service\Comment\Vote::class,
    'imageDeleteService'       => \App\Service\Image\Delete::class,
    'imageGetService'          => \App\Service\Image\Get::class,
    'imageUpdateService'       => \App\Service\Image\Update::class,
    'questionCommentService'   => \App\Service\Question\Comment::class,
    'questionDeleteService'    => \App\Service\Question\Delete::class,
    'questionFollowService'    => \App\Service\Question\Follow::class,
    'questionGetService'       => \App\Service\Question\Get::class,
    'questionUpdateService'    => \App\Service\Question\Update::class,
    'questionVoteService'      => \App\Service\Question\Vote::class,
    'reportDeleteService'      => \App\Service\Report\Delete::class,
    'reportGetService'         => \App\Service\Report\Get::class,
    'reportUpdateService'      => \App\Service\Report\Update::class,
    'topicCoverService'        => \App\Service\Topic\Cover::class,
    'topicDeleteService'       => \App\Service\Topic\Delete::class,
    'topicFollowService'       => \App\Service\Topic\Follow::class,
    'topicGetService'          => \App\Service\Topic\Get::class,
    'topicUpdateService'       => \App\Service\Topic\Update::class,
    'userAvatarService'        => \App\Service\User\Avatar::class,
    'userCoverService'         => \App\Service\User\Cover::class,
    'userDisableService'       => \App\Service\User\Disable::class,
    'userFollowService'        => \App\Service\User\Follow::class,
    'userGetService'           => \App\Service\User\Get::class,
    'userLoginService'         => \App\Service\User\Login::class,
    'userPasswordResetService' => \App\Service\User\PasswordReset::class,
    'userRegisterService'      => \App\Service\User\Register::class,
    'userUpdateService'        => \App\Service\User\Update::class,
    'captchaService'           => \App\Service\Captcha::class,
    'emailService'             => \App\Service\Email::class,
    'followService'            => \App\Service\Follow::class,
    'inboxService'             => \App\Service\Inbox::class,
    'notificationService'      => \App\Service\Notification::class,
    'optionService'            => \App\Service\Option::class,
    'requestService'           => \App\Service\Request::class,
    'roleService'              => \App\Service\Role::class,
    'throttleService'          => \App\Service\Throttle::class,
    'tokenService'             => \App\Service\Token::class,
    'urlService'               => \App\Service\Url::class,
    'voteService'              => \App\Service\Vote::class,
];

foreach ($names as $key => $name) {
    $container[$key] = static function ($container) use ($name) {
        return new $name($container);
    };
}
