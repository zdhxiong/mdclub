<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Container\ContainerInterface;
use Slim\Exception\ContainerValueNotFoundException;
use Slim\Http\Response;

/**
 * Class Controller
 *
 * @property-read \Slim\Views\PhpRenderer               view
 *
 * @property-read \App\Service\AnswerCommentService     AnswerCommentService
 * @property-read \App\Service\AnswerService            answerService
 * @property-read \App\Service\ArticleCommentService    articleCommentService
 * @property-read \App\Service\ArticleFollowService     articleFollowService
 * @property-read \App\Service\ArticleService           articleService
 * @property-read \App\Service\CaptchaService           captchaService
 * @property-read \App\Service\EmailService             emailService
 * @property-read \App\Service\ImageService             imageService
 * @property-read \App\Service\InboxService             inboxService
 * @property-read \App\Service\NotificationService      notificationService
 * @property-read \App\Service\OptionService            optionService
 * @property-read \App\Service\QuestionCommentService   questionCommentService
 * @property-read \App\Service\QuestionFollowService    questionFollowService
 * @property-read \App\Service\QuestionService          questionService
 * @property-read \App\Service\RoleService              roleService
 * @property-read \App\Service\ThrottleService          throttleService
 * @property-read \App\Service\TokenService             tokenService
 * @property-read \App\Service\TopicFollowService       topicFollowService
 * @property-read \App\Service\TopicService             topicService
 * @property-read \App\Service\UserAvatarService        userAvatarService
 * @property-read \App\Service\UserCoverService         userCoverService
 * @property-read \App\Service\UserFollowService        userFollowService
 * @property-read \App\Service\UserLoginService         userLoginService
 * @property-read \App\Service\UserPasswordResetService userPasswordResetService
 * @property-read \App\Service\UserRegisterService      userRegisterService
 * @property-read \App\Service\UserService              userService
 *
 * @package App\Controller
 */
class Controller
{
    /**
     * 容器实例
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Controller constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * 魔术方法，从容器中获取 Service 等
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        $nameUcFirst = ucfirst($name);

        $modules = [
            'App\\Service\\' . $nameUcFirst,
        ];

        foreach ($modules as $module) {
            if ($this->container->has($module)) {
                return $this->container->get($module);
            }
        }

        $libs = [
            'view' => \Slim\Views\PhpRenderer::class,
        ];

        if (isset($libs[$name]) && $this->container->has($libs[$name])) {
            return $this->container->get($libs[$name]);
        }

        throw new ContainerValueNotFoundException();
    }

    /**
     * 返回 API 成功时的 Response
     *
     * @param  Response  $response
     * @param  mixed     $data
     * @return Response
     */
    public function success(Response $response, $data = []): Response
    {
        $result = [
            'code' => 0,
        ];

        if (isset($data['data']) && isset($data['pagination'])) {
            $result['data'] = $data['data'];
            $result['pagination'] = $data['pagination'];
        } else {
            $result['data'] = $data;
        }

        return $response->withJson($result);
    }
}
