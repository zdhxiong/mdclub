<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use Psr\Container\ContainerInterface;
use Slim\App;

/**
 * 依赖
 */
class Dependencies
{
    /**
     * 需要放入容器中的类
     *
     * @var array
     */
    protected $values = [
        // 'request'               => ServerRequestInterface::class
        // 'response'              => ResponseInterface::class,
        // 'httpCache'             => \Slim\HttpCache\CacheProvider::class,
        // 'responseFactory',
        // 'callableResolver'
        'auth'                     => \MDClub\Library\Auth::class,
        'cache'                    => \MDClub\Library\Cache::class,
        'captcha'                  => \MDClub\Library\Captcha::class,
        'db'                       => \MDClub\Library\Db::class,
        'email'                    => \MDClub\Library\Email::class,
        'log'                      => \MDClub\Library\Log::class,
        'option'                   => \MDClub\Library\Option::class,
        'storage'                  => \MDClub\Library\Storage::class,
        'view'                     => \MDClub\Library\View::class,

        'answerModel'              => \MDClub\Model\Answer::class,
        'articleModel'             => \MDClub\Model\Article::class,
        'commentModel'             => \MDClub\Model\Comment::class,
        'followModel'              => \MDClub\Model\Follow::class,
        'imageModel'               => \MDClub\Model\Image::class,
        'inboxModel'               => \MDClub\Model\Inbox::class,
        'notificationModel'        => \MDClub\Model\Notification::class,
        'optionModel'              => \MDClub\Model\Option::class,
        'questionModel'            => \MDClub\Model\Question::class,
        'reportModel'              => \MDClub\Model\Report::class,
        'tokenModel'               => \MDClub\Model\Token::class,
        'topicModel'               => \MDClub\Model\Topic::class,
        'topicableModel'           => \MDClub\Model\Topicable::class,
        'userModel'                => \MDClub\Model\User::class,
        'voteModel'                => \MDClub\Model\Vote::class,

        'answerCommentService'     => \MDClub\Service\Answer\Comment::class,
        'answerCreateService'      => \MDClub\Service\Answer\Create::class,
        'answerDeleteService'      => \MDClub\Service\Answer\Delete::class,
        'answerGetService'         => \MDClub\Service\Answer\Get::class,
        'answerUpdateService'      => \MDClub\Service\Answer\Update::class,
        'answerVoteService'        => \MDClub\Service\Answer\Vote::class,
        'articleCommentService'    => \MDClub\Service\Article\Comment::class,
        'articleCreateService'     => \MDClub\Service\Article\Create::class,
        'articleDeleteService'     => \MDClub\Service\Article\Delete::class,
        'articleFollowService'     => \MDClub\Service\Article\Follow::class,
        'articleGetService'        => \MDClub\Service\Article\Get::class,
        'articleUpdateService'     => \MDClub\Service\Article\Update::class,
        'articleVoteService'       => \MDClub\Service\Article\Vote::class,
        'commentDeleteService'     => \MDClub\Service\Comment\Delete::class,
        'commentGetService'        => \MDClub\Service\Comment\Get::class,
        'commentUpdateService'     => \MDClub\Service\Comment\Update::class,
        'commentVoteService'       => \MDClub\Service\Comment\Vote::class,
        'imageDeleteService'       => \MDClub\Service\Image\Delete::class,
        'imageGetService'          => \MDClub\Service\Image\Get::class,
        'imageUpdateService'       => \MDClub\Service\Image\Update::class,
        'imageUploadService'       => \MDClub\Service\Image\Upload::class,
        'questionCommentService'   => \MDClub\Service\Question\Comment::class,
        'questionCreateService'    => \MDClub\Service\Question\Create::class,
        'questionDeleteService'    => \MDClub\Service\Question\Delete::class,
        'questionFollowService'    => \MDClub\Service\Question\Follow::class,
        'questionGetService'       => \MDClub\Service\Question\Get::class,
        'questionUpdateService'    => \MDClub\Service\Question\Update::class,
        'questionVoteService'      => \MDClub\Service\Question\Vote::class,
        'reportCreateService'      => \MDClub\Service\Report\Create::class,
        'reportDeleteService'      => \MDClub\Service\Report\Delete::class,
        'reportGetService'         => \MDClub\Service\Report\Get::class,
        'tokenCreateService'       => \MDClub\Service\Token\Create::class,
        'topicCreateService'       => \MDClub\Service\Topic\Create::class,
        'topicDeleteService'       => \MDClub\Service\Topic\Delete::class,
        'topicFollowService'       => \MDClub\Service\Topic\Follow::class,
        'topicGetService'          => \MDClub\Service\Topic\Get::class,
        'topicUpdateService'       => \MDClub\Service\Topic\Update::class,
        'userAvatarService'        => \MDClub\Service\User\Avatar::class,
        'userCoverService'         => \MDClub\Service\User\Cover::class,
        'userDisableService'       => \MDClub\Service\User\Disable::class,
        'userFollowService'        => \MDClub\Service\User\Follow::class,
        'userGetService'           => \MDClub\Service\User\Get::class,
        'userPasswordResetService' => \MDClub\Service\User\PasswordReset::class,
        'userRegisterService'      => \MDClub\Service\User\Register::class,
        'userUpdateService'        => \MDClub\Service\User\Update::class,

        'answerTransformer'        => \MDClub\Transformer\Answer::class,
        'articleTransformer'       => \MDClub\Transformer\Article::class,
        'commentTransformer'       => \MDClub\Transformer\Comment::class,
        'followTransformer'        => \MDClub\Transformer\Follow::class,
        'imageTransformer'         => \MDClub\Transformer\Image::class,
        'questionTransformer'      => \MDClub\Transformer\Question::class,
        'reportTransformer'        => \MDClub\Transformer\Report::class,
        'reportReasonTransformer'  => \MDClub\Transformer\ReportReason::class,
        'topicTransformer'         => \MDClub\Transformer\Topic::class,
        'userTransformer'          => \MDClub\Transformer\User::class,
        'voteTransformer'          => \MDClub\Transformer\Vote::class,
    ];

    /**
     * @param App $app
     */
    public function __construct(App $app)
    {
        /** @var Container $container */
        $container = $app->getContainer();

        $container->offsetSet('responseFactory', $app->getResponseFactory());
        $container->offsetSet('callableResolver', $app->getCallableResolver());

        // 将其他对象放入容器
        foreach ($this->values as $id => $className) {
            $container->offsetSet($id, function (ContainerInterface $container) use ($className) {
                return new $className($container);
            });
        }
    }
}
