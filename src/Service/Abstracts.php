<?php

declare(strict_types=1);

namespace MDClub\Service;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 服务抽象类
 *
 * @property-read ServerRequestInterface     $request
 * @property-read \MDClub\Library\Auth       $auth
 * @property-read \MDClub\Library\Cache      $cache
 * @property-read \MDClub\Library\Captcha    $captcha
 * @property-read \MDClub\Library\Email      $email
 * @property-read \MDClub\Library\Log        $log
 * @property-read \MDClub\Library\Option     $option
 * @property-read \MDClub\Library\Storage    $storage
 * @property-read \MDClub\Library\View       $view
 *
 * @property-read \MDClub\Model\Answer       $answerModel
 * @property-read \MDClub\Model\Article      $articleModel
 * @property-read \MDClub\Model\Comment      $commentModel
 * @property-read \MDClub\Model\Follow       $followModel
 * @property-read \MDClub\Model\Image        $imageModel
 * @property-read \MDClub\Model\Inbox        $inboxModel
 * @property-read \MDClub\Model\Notification $notificationModel
 * @property-read \MDClub\Model\Question     $questionModel
 * @property-read \MDClub\Model\Report       $reportModel
 * @property-read \MDClub\Model\Token        $tokenModel
 * @property-read \MDClub\Model\Topic        $topicModel
 * @property-read \MDClub\Model\Topicable    $topicableModel
 * @property-read \MDClub\Model\User         $userModel
 * @property-read \MDClub\Model\Vote         $voteModel
 *
 * @property-read Answer                     $answerService
 * @property-read AnswerComment              $answerCommentService
 * @property-read AnswerUpdate               $answerUpdateService
 * @property-read AnswerVote                 $answerVoteService
 * @property-read Article                    $articleService
 * @property-read ArticleComment             $articleCommentService
 * @property-read ArticleFollow              $articleFollowService
 * @property-read ArticleVote                $articleVoteService
 * @property-read Comment                    $commentService
 * @property-read CommentVote                $commentVoteService
 * @property-read Image                      $imageService
 * @property-read ImageDelete                $imageDeleteService
 * @property-read ImageUpdate                $imageUpdateService
 * @property-read ImageUpload                $imageUploadService
 * @property-read Inbox                      $inboxService
 * @property-read Notification               $notificationService
 * @property-read Question                   $questionService
 * @property-read QuestionComment            $questionCommentService
 * @property-read QuestionFollow             $questionFollowService
 * @property-read QuestionVote               $questionVoteService
 * @property-read Report                     $reportService
 * @property-read ReportCreate               $reportCreateService
 * @property-read Token                      $tokenService
 * @property-read TokenCreate                $tokenCreateService
 * @property-read Topic                      $topicService
 * @property-read TopicDelete                $topicDeleteService
 * @property-read TopicFollow                $topicFollowService
 * @property-read TopicUpdate                $topicUpdateService
 * @property-read User                       $userService
 * @property-read UserAvatar                 $userAvatarService
 * @property-read UserCover                  $userCoverService
 * @property-read UserDisable                $userDisableService
 * @property-read UserFollow                 $userFollowService
 * @property-read UserPasswordReset          $userPasswordResetService
 * @property-read UserRegister               $userRegisterService
 * @property-read UserUpdate                 $userUpdateService
 */
abstract class Abstracts
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __get(string $name)
    {
        return $this->container->get($name);
    }
}
