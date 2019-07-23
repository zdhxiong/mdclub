<?php

declare(strict_types=1);

namespace MDClub\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * 控制器抽象类
 *
 * @property-read ServerRequestInterface             $request
 * @property-read ResponseInterface                  $response
 * @property-read \MDClub\Library\Auth               $auth
 * @property-read \MDClub\Library\View               $view
 *
 * @property-read \MDClub\Service\Answer             $answerService
 * @property-read \MDClub\Service\AnswerComment      $answerCommentService
 * @property-read \MDClub\Service\AnswerUpdate       $answerUpdateService
 * @property-read \MDClub\Service\AnswerVote         $answerVoteService
 * @property-read \MDClub\Service\Article            $articleService
 * @property-read \MDClub\Service\ArticleComment     $articleCommentService
 * @property-read \MDClub\Service\ArticleFollow      $articleFollowService
 * @property-read \MDClub\Service\ArticleVote        $articleVoteService
 * @property-read \MDClub\Service\Comment            $commentService
 * @property-read \MDClub\Service\CommentVote        $commentVoteService
 * @property-read \MDClub\Service\Image              $imageService
 * @property-read \MDClub\Service\ImageDelete        $imageDeleteService
 * @property-read \MDClub\Service\ImageUpdate        $imageUpdateService
 * @property-read \MDClub\Service\ImageUpload        $imageUploadService
 * @property-read \MDClub\Service\Inbox              $inboxService
 * @property-read \MDClub\Service\Notification       $notificationService
 * @property-read \MDClub\Service\Question           $questionService
 * @property-read \MDClub\Service\QuestionComment    $questionCommentService
 * @property-read \MDClub\Service\QuestionFollow     $questionFollowService
 * @property-read \MDClub\Service\QuestionVote       $questionVoteService
 * @property-read \MDClub\Service\Report             $reportService
 * @property-read \MDClub\Service\ReportCreate       $reportCreateService
 * @property-read \MDClub\Service\Token              $tokenService
 * @property-read \MDClub\Service\TokenCreate        $tokenCreateService
 * @property-read \MDClub\Service\Topic              $topicService
 * @property-read \MDClub\Service\TopicDelete        $topicDeleteService
 * @property-read \MDClub\Service\TopicFollow        $topicFollowService
 * @property-read \MDClub\Service\TopicUpdate        $topicUpdateService
 * @property-read \MDClub\Service\User               $userService
 * @property-read \MDClub\Service\UserAvatar         $userAvatarService
 * @property-read \MDClub\Service\UserCover          $userCoverService
 * @property-read \MDClub\Service\UserDisable        $userDisableService
 * @property-read \MDClub\Service\UserFollow         $userFollowService
 * @property-read \MDClub\Service\UserPasswordReset  $userPasswordResetService
 * @property-read \MDClub\Service\UserRegister       $userRegisterService
 * @property-read \MDClub\Service\UserUpdate         $userUpdateService
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

    /**
     * 渲染 HTML 模板
     *
     * @param  string             $template
     * @param  array              $data
     * @return ResponseInterface
     */
    public function render(string $template, array $data = []): ResponseInterface
    {
        return $this->view->render($this->response, $template, $data);
    }
}
