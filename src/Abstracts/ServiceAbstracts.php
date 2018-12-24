<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Traits\UrlTraits;
use Psr\Container\ContainerInterface;
use App\Helper\ArrayHelper;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class Service
 *
 * @property-read \Slim\Http\Request                      request
 * @property-read \Slim\Interfaces\RouterInterface        router
 *
 * @property-read \App\Library\Cache                      cache
 * @property-read \App\Library\FileCache                  fileCache
 * @property-read \App\Library\KvCache                    kvCache
 * @property-read \App\Library\Storage                    storage
 * @property-read \App\Library\View                       view
 * @property-read \App\Library\Logger                     logger
 *
 * @property-read \App\Model\AnswerModel                  answerModel
 * @property-read \App\Model\ArticleModel                 articleModel
 * @property-read \App\Model\CommentModel                 commentModel
 * @property-read \App\Model\FollowModel                  followModel
 * @property-read \App\Model\ImageModel                   imageModel
 * @property-read \App\Model\InboxModel                   inboxModel
 * @property-read \App\Model\NotificationModel            notificationModel
 * @property-read \App\Model\OptionModel                  optionModel
 * @property-read \App\Model\QuestionModel                questionModel
 * @property-read \App\Model\ReportModel                  reportModel
 * @property-read \App\Model\TokenModel                   tokenModel
 * @property-read \App\Model\TopicableModel               topicableModel
 * @property-read \App\Model\TopicModel                   topicModel
 * @property-read \App\Model\UserModel                    userModel
 * @property-read \App\Model\VoteModel                    voteModel
 *
 * @property-read \App\Service\AnswerService              answerService
 * @property-read \App\Service\ArticleService             articleService
 * @property-read \App\Service\CaptchaService             captchaService
 * @property-read \App\Service\CommentService             commentService
 * @property-read \App\Service\EmailService               emailService
 * @property-read \App\Service\FollowService              followService
 * @property-read \App\Service\ImageService               imageService
 * @property-read \App\Service\InboxService               inboxService
 * @property-read \App\Service\NotificationService        notificationService
 * @property-read \App\Service\OptionService              optionService
 * @property-read \App\Service\QuestionService            questionService
 * @property-read \App\Service\ReportService              reportService
 * @property-read \App\Service\RoleService                roleService
 * @property-read \App\Service\ThrottleService            throttleService
 * @property-read \App\Service\TokenService               tokenService
 * @property-read \App\Service\TopicService               topicService
 * @property-read \App\Service\UserAvatarService          userAvatarService
 * @property-read \App\Service\UserCoverService           userCoverService
 * @property-read \App\Service\UserLoginService           userLoginService
 * @property-read \App\Service\UserPasswordResetService   userPasswordResetService
 * @property-read \App\Service\UserRegisterService        userRegisterService
 * @property-read \App\Service\UserService                userService
 * @property-read \App\Service\VoteService                voteService
 *
 * @package App\Service
 */
abstract class ServiceAbstracts
{
    use UrlTraits;

    /**
     * 容器实例
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * 当前 Model 实例
     */
    protected $currentModel;

    /**
     * 隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return [];
    }

    /**
     * 允许排序的字段
     *
     * @return array
     */
    public function getAllowOrderFields(): array
    {
        return [];
    }

    /**
     * 允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return [];
    }

    /**
     * ServiceAbstracts constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $serviceName = get_class($this);
        $modelName = str_replace('\\Service\\', '\\Model\\', substr($serviceName, 0, -7) . 'Model');

        if ($this->container->has($modelName)) {
            $this->currentModel = $this->container->get($modelName);
        }
    }

    /**
     * 魔术方法，从容器中获取 Model、Service 等
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        $modules = [
            'App\\Model\\' . ucfirst($name),
            'App\\Service\\' . ucfirst($name),
            'App\\Library\\' . ucfirst($name),
            'request',
            'router',
        ];

        foreach ($modules as $module) {
            if ($this->container->has($module)) {
                return $this->container->get($module);
            }
        }

        throw new ContainerValueNotFoundException();
    }

    /**
     * 获取查询列表时的排序
     *
     * order=field 表示 field ASC
     * order=-field 表示 field DESC
     *
     * @param  array $defaultOrder 默认排序；query 参数不存在时，该参数才生效
     * @return array
     */
    protected function getOrder(array $defaultOrder = []): array
    {
        $result = [];
        $order = $this->request->getQueryParam('order');

        if ($order) {
            if (strpos($order, '-') === 0) {
                $result[substr($order, 1)] = 'DESC';
            } else {
                $result[$order] = 'ASC';
            }

            $result = ArrayHelper::filter($result, $this->getAllowOrderFields());
        }

        if (!$result) {
            $result = $defaultOrder;
        }

        return $result;
    }

    /**
     * 查询列表时的条件
     *
     * @param  array $defaultFilter 默认条件。该条件将覆盖 query 中的同名参数
     * @return array
     */
    protected function getWhere(array $defaultFilter = []): array
    {
        $result = $this->request->getQueryParams();
        $result = ArrayHelper::filter($result, $this->getAllowFilterFields());
        $result = array_merge($result, $defaultFilter);

        return $result;
    }
}
