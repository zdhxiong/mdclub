<?php

declare(strict_types=1);

namespace App\Abstracts;

use Psr\Container\ContainerInterface;

/**
 * 容器抽象类：将容器中的对象直接放在 $this 下
 *
 * @property-read \Slim\Http\Request                  request
 * @property-read \Slim\Http\Response                 response
 *
 * @property-read \Slim\HttpCache\CacheProvider       httpCache
 * @property-read \App\Library\Cache                  cache
 * @property-read \App\Library\Db                     db
 * @property-read \App\Library\Logger                 logger
 * @property-read \App\Library\Storage                storage
 * @property-read \App\Library\View                   view
 * @property-read \App\Library\Redis                  redis
 *
 * @property-read \App\Model\Answer                   answerModel
 * @property-read \App\Model\Article                  articleModel
 * @property-read \App\Model\Comment                  commentModel
 * @property-read \App\Model\Follow                   followModel
 * @property-read \App\Model\Image                    imageModel
 * @property-read \App\Model\Inbox                    inboxModel
 * @property-read \App\Model\Notification             notificationModel
 * @property-read \App\Model\Option                   optionModel
 * @property-read \App\Model\Question                 questionModel
 * @property-read \App\Model\Report                   reportModel
 * @property-read \App\Model\Token                    tokenModel
 * @property-read \App\Model\Topicable                topicableModel
 * @property-read \App\Model\Topic                    topicModel
 * @property-read \App\Model\User                     userModel
 * @property-read \App\Model\Vote                     voteModel
 *
 * @property-read \App\Service\Answer\Comment         answerCommentService
 * @property-read \App\Service\Answer\Delete          answerDeleteService
 * @property-read \App\Service\Answer\Get             answerGetService
 * @property-read \App\Service\Answer\Update          answerUpdateService
 * @property-read \App\Service\Answer\Vote            answerVoteService
 * @property-read \App\Service\Article\Comment        articleCommentService
 * @property-read \App\Service\Article\Delete         articleDeleteService
 * @property-read \App\Service\Article\Follow         articleFollowService
 * @property-read \App\Service\Article\Get            articleGetService
 * @property-read \App\Service\Article\Update         articleUpdateService
 * @property-read \App\Service\Article\Vote           articleVoteService
 * @property-read \App\Service\Comment\Delete         commentDeleteService
 * @property-read \App\Service\Comment\Get            commentGetService
 * @property-read \App\Service\Comment\Update         commentUpdateService
 * @property-read \App\Service\Comment\Vote           commentVoteService
 * @property-read \App\Service\Image\Delete           imageDeleteService
 * @property-read \App\Service\Image\Get              imageGetService
 * @property-read \App\Service\Image\Update           imageUpdateService
 * @property-read \App\Service\Question\Comment       questionCommentService
 * @property-read \App\Service\Question\Delete        questionDeleteService
 * @property-read \App\Service\Question\Follow        questionFollowService
 * @property-read \App\Service\Question\Get           questionGetService
 * @property-read \App\Service\Question\Update        questionUpdateService
 * @property-read \App\Service\Question\Vote          questionVoteService
 * @property-read \App\Service\Report\Delete          reportDeleteService
 * @property-read \App\Service\Report\Get             reportGetService
 * @property-read \App\Service\Report\Update          reportUpdateService
 * @property-read \App\Service\Topic\Cover            topicCoverService
 * @property-read \App\Service\Topic\Delete           topicDeleteService
 * @property-read \App\Service\Topic\Follow           topicFollowService
 * @property-read \App\Service\Topic\Get              topicGetService
 * @property-read \App\Service\Topic\Update           topicUpdateService
 * @property-read \App\Service\User\Avatar            userAvatarService
 * @property-read \App\Service\User\Cover             userCoverService
 * @property-read \App\Service\User\Disable           userDisableService
 * @property-read \App\Service\User\Follow            userFollowService
 * @property-read \App\Service\User\Get               userGetService
 * @property-read \App\Service\User\Login             userLoginService
 * @property-read \App\Service\User\PasswordReset     userPasswordResetService
 * @property-read \App\Service\User\Register          userRegisterService
 * @property-read \App\Service\User\Update            userUpdateService
 * @property-read \App\Service\Captcha                captchaService
 * @property-read \App\Service\Email                  emailService
 * @property-read \App\Service\Follow                 followService
 * @property-read \App\Service\Inbox                  inboxService
 * @property-read \App\Service\Notification           notificationService
 * @property-read \App\Service\Option                 optionService
 * @property-read \App\Service\Request                requestService
 * @property-read \App\Service\Role                   roleService
 * @property-read \App\Service\Throttle               throttleService
 * @property-read \App\Service\Token                  tokenService
 * @property-read \App\Service\Url                    urlService
 * @property-read \App\Service\Vote                   voteService
 */
abstract class ContainerAbstracts
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * 可以直接通过 $this-> 调用容器内的对象
     *
     * @param  string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        return $this->container->get($name);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set(string $name, $value)
    {
        // TODO: Implement __set() method.
    }

    /**
     * @param string $name
     */
    public function __isset(string $name)
    {
        // TODO: Implement __isset() method.
    }
}
