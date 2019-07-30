<?php

declare(strict_types=1);

namespace MDClub\Traits;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 便于 IDE 提示
 *
 * @property      ServerRequestInterface             $request
 * @property      ResponseInterface                  $response
 * @property-read \MDClub\Library\Auth               $auth
 * @property-read \MDClub\Library\Cache              $cache
 * @property-read \MDClub\Library\Captcha            $captcha
 * @property-read \MDClub\Library\Db                 $db
 * @property-read \MDClub\Library\Email              $email
 * @property-read \MDClub\Library\Log                $log
 * @property-read \MDClub\Library\Option             $option
 * @property-read \MDClub\Library\Storage            $storage
 * @property-read \MDClub\Library\View               $view
 *
 * @property-read \MDClub\Service\Answer\Comment     $answerCommentService
 * @property-read \MDClub\Service\Answer\Create      $answerCreateService
 * @property-read \MDClub\Service\Answer\Delete      $answerDeleteService
 * @property-read \MDClub\Service\Answer\Get         $answerGetService
 * @property-read \MDClub\Service\Answer\Update      $answerUpdateService
 * @property-read \MDClub\Service\Answer\Vote        $answerVoteService
 * @property-read \MDClub\Service\Article\Comment    $articleCommentService
 * @property-read \MDClub\Service\Article\Create     $articleCreateService
 * @property-read \MDClub\Service\Article\Delete     $articleDeleteService
 * @property-read \MDClub\Service\Article\Follow     $articleFollowService
 * @property-read \MDClub\Service\Article\Get        $articleGetService
 * @property-read \MDClub\Service\Article\Update     $articleUpdateService
 * @property-read \MDClub\Service\Article\Vote       $articleVoteService
 * @property-read \MDClub\Service\Comment\Delete     $commentDeleteService
 * @property-read \MDClub\Service\Comment\Get        $commentGetService
 * @property-read \MDClub\Service\Comment\Update     $commentUpdateService
 * @property-read \MDClub\Service\Comment\Vote       $commentVoteService
 * @property-read \MDClub\Service\Image\Delete       $imageDeleteService
 * @property-read \MDClub\Service\Image\Get          $imageGetService
 * @property-read \MDClub\Service\Image\Update       $imageUpdateService
 * @property-read \MDClub\Service\Image\Upload       $imageUploadService
 * @property-read \MDClub\Service\Question\Comment   $questionCommentService
 * @property-read \MDClub\Service\Question\Create    $questionCreateService
 * @property-read \MDClub\Service\Question\Delete    $questionDeleteService
 * @property-read \MDClub\Service\Question\Follow    $questionFollowService
 * @property-read \MDClub\Service\Question\Get       $questionGetService
 * @property-read \MDClub\Service\Question\Update    $questionUpdateService
 * @property-read \MDClub\Service\Question\Vote      $questionVoteService
 * @property-read \MDClub\Service\Report\Create      $reportCreateService
 * @property-read \MDClub\Service\Report\Delete      $reportDeleteService
 * @property-read \MDClub\Service\Report\Get         $reportGetService
 * @property-read \MDClub\Service\Token\Create       $tokenCreateService
 * @property-read \MDClub\Service\Topic\Create       $topicCreateService
 * @property-read \MDClub\Service\Topic\Delete       $topicDeleteService
 * @property-read \MDClub\Service\Topic\Follow       $topicFollowService
 * @property-read \MDClub\Service\Topic\Get          $topicGetService
 * @property-read \MDClub\Service\Topic\Update       $topicUpdateService
 * @property-read \MDClub\Service\User\Avatar        $userAvatarService
 * @property-read \MDClub\Service\User\Cover         $userCoverService
 * @property-read \MDClub\Service\User\Disable       $userDisableService
 * @property-read \MDClub\Service\User\Follow        $userFollowService
 * @property-read \MDClub\Service\User\Get           $userGetService
 * @property-read \MDClub\Service\User\PasswordReset $userPasswordResetService
 * @property-read \MDClub\Service\User\Register      $userRegisterService
 * @property-read \MDClub\Service\User\Update        $userUpdateService
 */
trait ContainerProperty{}
