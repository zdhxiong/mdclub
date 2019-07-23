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
        // 'httpCache'                => \Slim\HttpCache\CacheProvider::class,
        'auth'                     => \MDClub\Library\Auth::class,
        'cache'                    => \MDClub\Library\Cache::class,
        'captcha'                  => \MDClub\Library\Captcha::class,
        'db'                       => \MDClub\Library\Db::class,
        'email'                    => \MDClub\Library\Email::class,
        'log'                      => \MDClub\Library\Log::class,
        'option'                   => \MDClub\Library\Option::class,
        'redis'                    => \MDClub\Library\Redis::class,
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

        'answerService'            => \MDClub\Service\Answer::class,
        'answerCommentService'     => \MDClub\Service\AnswerComment::class,
        'answerUpdateService'      => \MDClub\Service\AnswerUpdate::class,
        'answerVoteService'        => \MDClub\Service\AnswerVote::class,
        'articleService'           => \MDClub\Service\Article::class,
        'articleCommentService'    => \MDClub\Service\ArticleComment::class,
        'articleFollowService'     => \MDClub\Service\ArticleFollow::class,
        'articleVoteService'       => \MDClub\Service\ArticleVote::class,
        'commentService'           => \MDClub\Service\Comment::class,
        'commentVoteService'       => \MDClub\Service\CommentVote::class,
        'imageService'             => \MDClub\Service\Image::class,
        'imageDeleteService'       => \MDClub\Service\ImageDelete::class,
        'imageUpdateService'       => \MDClub\Service\ImageUpdate::class,
        'imageUploadService'       => \MDClub\Service\ImageUpload::class,
        'inboxService'             => \MDClub\Service\Inbox::class,
        'notificationService'      => \MDClub\Service\Notification::class,
        'questionService'          => \MDClub\Service\Question::class,
        'questionCommentService'   => \MDClub\Service\QuestionComment::class,
        'questionFollowService'    => \MDClub\Service\QuestionFollow::class,
        'questionVoteService'      => \MDClub\Service\QuestionVote::class,
        'reportService'            => \MDClub\Service\Report::class,
        'reportCreateService'      => \MDClub\Service\ReportCreate::class,
        'tokenService'             => \MDClub\Service\Token::class,
        'tokenCreateService'       => \MDClub\Service\TokenCreate::class,
        'topicService'             => \MDClub\Service\Topic::class,
        'topicDeleteService'       => \MDClub\Service\TopicDelete::class,
        'topicFollowService'       => \MDClub\Service\TopicFollow::class,
        'topicUpdateService'       => \MDClub\Service\TopicUpdate::class,
        'userService'              => \MDClub\Service\User::class,
        'userAvatarService'        => \MDClub\Service\UserAvatar::class,
        'userCoverService'         => \MDClub\Service\UserCover::class,
        'userDisableService'       => \MDClub\Service\UserDisable::class,
        'userFollowService'        => \MDClub\Service\UserFollow::class,
        'userPasswordResetService' => \MDClub\Service\UserPasswordReset::class,
        'userRegisterService'      => \MDClub\Service\UserRegister::class,
        'userUpdateService'        => \MDClub\Service\UserUpdate::class,

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

        // 将其他对象放入容器
        foreach ($this->values as $id => $className) {
            $container->offsetSet($id, function (ContainerInterface $container) use ($className) {
                return new $className($container);
            });
        }
    }
}
