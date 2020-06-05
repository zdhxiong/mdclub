<?php

declare(strict_types=1);

namespace MDClub\Initializer;

use MDClub\Library\Auth;
use MDClub\Library\Cache;
use MDClub\Library\Captcha;
use MDClub\Library\Db;
use MDClub\Library\Email;
use MDClub\Library\Log;
use MDClub\Library\Option;
use MDClub\Library\Storage;
use MDClub\Library\Throttle;
use MDClub\Library\View;
use MDClub\Model\Answer as AnswerModel;
use MDClub\Model\Article as ArticleModel;
use MDClub\Model\Comment as CommentModel;
use MDClub\Model\Follow as FollowModel;
use MDClub\Model\Image as ImageModel;
use MDClub\Model\Inbox as InboxModel;
use MDClub\Model\Notification as NotificationModel;
use MDClub\Model\Option as OptionModel;
use MDClub\Model\Question as QuestionModel;
use MDClub\Model\Report as ReportModel;
use MDClub\Model\Token as TokenModel;
use MDClub\Model\Topic as TopicModel;
use MDClub\Model\Topicable as TopicableModel;
use MDClub\Model\User as UserModel;
use MDClub\Model\Vote as VoteModel;
use MDClub\Service\Answer as AnswerService;
use MDClub\Service\Article as ArticleService;
use MDClub\Service\Comment as CommentService;
use MDClub\Service\Image as ImageService;
use MDClub\Service\Notification as NotificationService;
use MDClub\Service\Question as QuestionService;
use MDClub\Service\Report as ReportService;
use MDClub\Service\Stats as StatsService;
use MDClub\Service\Token as TokenService;
use MDClub\Service\Topic as TopicService;
use MDClub\Service\User as UserService;
use MDClub\Service\UserAvatar as UserAvatarService;
use MDClub\Service\UserCover as UserCoverService;
use MDClub\Transformer\Answer as AnswerTransformer;
use MDClub\Transformer\Article as ArticleTransformer;
use MDClub\Transformer\Comment as CommentTransformer;
use MDClub\Transformer\Follow as FollowTransformer;
use MDClub\Transformer\Image as ImageTransformer;
use MDClub\Transformer\Notification as NotificationTransformer;
use MDClub\Transformer\Question as QuestionTransformer;
use MDClub\Transformer\Report as ReportTransformer;
use MDClub\Transformer\ReportReason as ReportReasonTransformer;
use MDClub\Transformer\Topic as TopicTransformer;
use MDClub\Transformer\User as UserTransformer;
use MDClub\Transformer\Vote as VoteTransformer;
use MDClub\Validator\Answer as AnswerValidator;
use MDClub\Validator\Article as ArticleValidator;
use MDClub\Validator\Comment as CommentValidator;
use MDClub\Validator\Email as EmailValidator;
use MDClub\Validator\Image as ImageValidator;
use MDClub\Validator\Option as OptionValidator;
use MDClub\Validator\Question as QuestionValidator;
use MDClub\Validator\Report as ReportValidator;
use MDClub\Validator\Token as TokenValidator;
use MDClub\Validator\Topic as TopicValidator;
use MDClub\Validator\User as UserValidator;
use MDClub\Validator\UserAvatar as UserAvatarValidator;
use MDClub\Validator\UserCover as UserCoverValidator;

/**
 * 依赖
 */
class Dependencies
{
    public function __construct()
    {
        $container = App::$container;

        $container->add(Auth::class);
        $container->add(Cache::class);
        $container->add(Captcha::class);
        $container->add(Db::class);
        $container->add(Email::class);
        $container->add(Log::class);
        $container->add(Option::class);
        $container->add(Storage::class);
        $container->add(Throttle::class);
        $container->add(View::class);

        // 模型类
        $container->add(AnswerModel::class);
        $container->add(ArticleModel::class);
        $container->add(CommentModel::class);
        $container->add(FollowModel::class);
        $container->add(ImageModel::class);
        $container->add(InboxModel::class);
        $container->add(NotificationModel::class);
        $container->add(OptionModel::class);
        $container->add(QuestionModel::class);
        $container->add(ReportModel::class);
        $container->add(TokenModel::class);
        $container->add(TopicModel::class);
        $container->add(TopicableModel::class);
        $container->add(UserModel::class);
        $container->add(VoteModel::class);

        // 服务类
        $container->add(AnswerService::class);
        $container->add(ArticleService::class);
        $container->add(CommentService::class);
        $container->add(ImageService::class);
        $container->add(NotificationService::class);
        $container->add(QuestionService::class);
        $container->add(ReportService::class);
        $container->add(StatsService::class);
        $container->add(TokenService::class);
        $container->add(TopicService::class);
        $container->add(UserService::class);
        $container->add(UserAvatarService::class);
        $container->add(UserCoverService::class);

        // 转换器类
        $container->add(AnswerTransformer::class);
        $container->add(ArticleTransformer::class);
        $container->add(CommentTransformer::class);
        $container->add(FollowTransformer::class);
        $container->add(ImageTransformer::class);
        $container->add(NotificationTransformer::class);
        $container->add(QuestionTransformer::class);
        $container->add(ReportTransformer::class);
        $container->add(ReportReasonTransformer::class);
        $container->add(TopicTransformer::class);
        $container->add(UserTransformer::class);
        $container->add(VoteTransformer::class);

        // 验证器类
        $container->add(AnswerValidator::class);
        $container->add(ArticleValidator::class);
        $container->add(CommentValidator::class);
        $container->add(EmailValidator::class);
        $container->add(ImageValidator::class);
        $container->add(OptionValidator::class);
        $container->add(QuestionValidator::class);
        $container->add(ReportValidator::class);
        $container->add(TokenValidator::class);
        $container->add(TopicValidator::class);
        $container->add(UserValidator::class);
        $container->add(UserAvatarValidator::class);
        $container->add(UserCoverValidator::class);
    }
}
