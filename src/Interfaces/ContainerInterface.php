<?php

declare(strict_types=1);

namespace App\Interfaces;

/**
 * Interface ContainerInterface
 *
 * @property-read array                               settings
 * @property-read \Slim\Http\Request                  request
 * @property-read \Slim\Http\Response                 response
 * @property-read \Slim\Interfaces\RouterInterface    router
 *
 * @property-read \App\Library\Cache                  cache
 * @property-read \App\Library\Db                     db
 * @property-read \App\Library\Logger                 logger
 * @property-read \App\Library\Storage                storage
 * @property-read \App\Library\View                   view
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
 * @property-read \App\Service\Answer                 answerService
 * @property-read \App\Service\Article                articleService
 * @property-read \App\Service\Captcha                captchaService
 * @property-read \App\Service\Comment                commentService
 * @property-read \App\Service\Email                  emailService
 * @property-read \App\Service\Follow                 followService
 * @property-read \App\Service\Image                  imageService
 * @property-read \App\Service\Inbox                  inboxService
 * @property-read \App\Service\Notification           notificationService
 * @property-read \App\Service\Option                 optionService
 * @property-read \App\Service\Question               questionService
 * @property-read \App\Service\Report                 reportService
 * @property-read \App\Service\Role                   roleService
 * @property-read \App\Service\Throttle               throttleService
 * @property-read \App\Service\Token                  tokenService
 * @property-read \App\Service\Topic                  topicService
 * @property-read \App\Service\UserAvatar             userAvatarService
 * @property-read \App\Service\UserCover              userCoverService
 * @property-read \App\Service\UserLogin              userLoginService
 * @property-read \App\Service\UserPasswordReset      userPasswordResetService
 * @property-read \App\Service\UserRegister           userRegisterService
 * @property-read \App\Service\User                   userService
 * @property-read \App\Service\Vote                   voteService
 */
interface ContainerInterface extends \Psr\Container\ContainerInterface
{

}
