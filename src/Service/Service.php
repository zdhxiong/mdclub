<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Container\ContainerInterface;
use App\Helper\ArrayHelper;
use Slim\Exception\ContainerValueNotFoundException;

/**
 * Class Service
 *
 * @property-read \Psr\SimpleCache\CacheInterface         filesystemCache
 * @property-read \Psr\SimpleCache\CacheInterface         distributedCache
 * @property-read \Psr\SimpleCache\CacheInterface         cache
 * @property-read \Psr\Log\LoggerInterface                logger
 * @property-read \League\Flysystem\FilesystemInterface   filesystem
 * @property-read \Slim\Http\Request                      request
 * @property-read \Slim\Interfaces\RouterInterface        router
 * @property-read \Slim\Views\PhpRenderer                 view
 *
 * @property-read \App\Model\AnswerModel                  answerModel
 * @property-read \App\Model\ArticleModel                 articleModel
 * @property-read \App\Model\CommentModel                 commentModel
 * @property-read \App\Model\FollowableModel              followableModel
 * @property-read \App\Model\ImageModel                   imageModel
 * @property-read \App\Model\InboxModel                   inboxModel
 * @property-read \App\Model\NotificationModel            notificationModel
 * @property-read \App\Model\OptionModel                  optionModel
 * @property-read \App\Model\QuestionModel                questionModel
 * @property-read \App\Model\TokenModel                   tokenModel
 * @property-read \App\Model\TopicableModel               topicableModel
 * @property-read \App\Model\TopicModel                   topicModel
 * @property-read \App\Model\UserModel                    userModel
 *
 * @property-read AnswerCommentService                    answerCommentService
 * @property-read AnswerService                           answerService
 * @property-read ArticleCommentService                   articleCommentService
 * @property-read ArticleFollowService                    articleFollowService
 * @property-read ArticleService                          articleService
 * @property-read CaptchaService                          captchaService
 * @property-read EmailService                            emailService
 * @property-read InboxService                            inboxService
 * @property-read NotificationService                     notificationService
 * @property-read OptionService                           optionService
 * @property-read QuestionCommentService                  questionCommentService
 * @property-read QuestionFollowService                   questionFollowService
 * @property-read QuestionService                         questionService
 * @property-read RoleService                             roleService
 * @property-read ThrottleService                         throttleService
 * @property-read TokenService                            tokenService
 * @property-read TopicFollowService                      topicFollowService
 * @property-read TopicService                            topicService
 * @property-read UserAvatarService                       userAvatarService
 * @property-read UserCoverService                        userCoverService
 * @property-read UserFollowService                       userFollowService
 * @property-read UserLoginService                        userLoginService
 * @property-read UserPasswordResetService                userPasswordResetService
 * @property-read UserRegisterService                     userRegisterService
 * @property-read UserService                             userService
 *
 * @package App\Service
 */
class Service
{
    /**
     * 容器实例
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * 隐私字段
     *
     * @return array
     */
    protected function getPrivacyFields(): array
    {
        return [];
    }

    /**
     * 允许排序的字段
     *
     * @return array
     */
    protected function getAllowOrderFields(): array
    {
        return [];
    }

    /**
     * 允许搜素的字段
     *
     * @return array
     */
    protected function getAllowFilterFields(): array
    {
        return [];
    }

    /**
     * Service constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * 魔术方法，从容器中获取 Model、Service、Validator 等
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        $nameUcFirst = ucfirst($name);

        $modules = [
            'App\\Model\\' . $nameUcFirst,
            'App\\Service\\' . $nameUcFirst,
            'App\\Validator\\' . $nameUcFirst,
        ];

        foreach ($modules as $module) {
            if ($this->container->has($module)) {
                return $this->container->get($module);
            }
        }

        $libs = [
            'filesystemCache'   => \App\Interfaces\FilesystemCacheInterface::class,
            'distributedCache'  => \App\Interfaces\DistributedCacheInterface::class,
            'cache'             => \Psr\SimpleCache\CacheInterface::class,
            'logger'            => \Psr\Log\LoggerInterface::class,
            'filesystem'        => \League\Flysystem\FilesystemInterface::class,
            'request'           => 'request',
            'router'            => 'router',
            'view'              => \Slim\Views\PhpRenderer::class,
        ];

        if (isset($libs[$name]) && $this->container->has($libs[$name])) {
            return $this->container->get($libs[$name]);
        }

        throw new ContainerValueNotFoundException();
    }

    /**
     * 获取查询列表时的排序
     *
     * order=field1,-field2 表示 field1 ASC, field2 DESC
     *
     * @param  array $defaultOrder 默认排序；query 参数不存在时，该参数才生效
     * @return array
     */
    protected function getOrder(array $defaultOrder = []): array
    {
        $order = [];
        $orderQueryParam = $this->request->getQueryParam('order');

        if ($orderQueryParam) {
            $conditions = explode(',', $orderQueryParam);

            foreach ($conditions as $condition) {
                if (substr($condition, 0, 1) == '-') {
                    $order[strtolower(substr($condition, 1))] = 'DESC';
                } else {
                    $order[strtolower($condition)] = 'ASC';
                }
            }
        }

        if (!$order) {
            $order = $defaultOrder;
        }

        $order = ArrayHelper::filter($order, $this->getAllowOrderFields());

        return $order;
    }

    /**
     * 查询列表时的条件
     *
     * filter=field1=value1,field2>value2,field3<=value3 表示 field1=value1 AND field2>value2 AND field3<=value3
     *
     * @param  array $defaultFilter 默认条件。query 中存在相同键名的参数时，将覆盖默认条件
     * @return array
     */
    protected function getWhere(array $defaultFilter = []): array
    {
        $filter = [];
        $filterQueryParam = $this->request->getQueryParam('filter');

        if ($filterQueryParam) {
            $conditions = explode(',', $filterQueryParam);
            $separators = ['>=', '<=', '>', '<', '='];

            foreach ($conditions as $condition) {
                foreach ($separators as $separator) {
                    if (strpos($condition, $separator) > 0) {
                        [$field, $value] = explode($separator, $condition);
                        $filter[$field] = [$separator, $value];

                        break;
                    }
                }
            }
        }

        $filter = ArrayHelper::filter($filter, $this->getAllowFilterFields());
        $filter = array_merge($defaultFilter, $filter);

        foreach ($filter as $key => $value) {
            if (is_array($value)) {
                unset($filter[$key]);
                $filter[$key . '[' . $value[0] . ']'] = $value[1];
            }
        }

        return $filter;
    }
}
