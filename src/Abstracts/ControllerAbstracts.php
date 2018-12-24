<?php

declare(strict_types=1);

namespace App\Abstracts;

use App\Traits\UrlTraits;
use Psr\Container\ContainerInterface;
use Slim\Exception\ContainerValueNotFoundException;
use Slim\Http\Response;

/**
 * Class Controller
 *
 * @property-read \Slim\Http\Request                    request
 * @property-read \App\Library\View                     view
 *
 * @property-read \App\Service\AnswerService            answerService
 * @property-read \App\Service\ArticleService           articleService
 * @property-read \App\Service\CaptchaService           captchaService
 * @property-read \App\Service\CommentService           commentService
 * @property-read \App\Service\EmailService             emailService
 * @property-read \App\Service\FollowService            followService
 * @property-read \App\Service\ImageService             imageService
 * @property-read \App\Service\InboxService             inboxService
 * @property-read \App\Service\NotificationService      notificationService
 * @property-read \App\Service\OptionService            optionService
 * @property-read \App\Service\QuestionService          questionService
 * @property-read \App\Service\ReportService            reportService
 * @property-read \App\Service\RoleService              roleService
 * @property-read \App\Service\ThrottleService          throttleService
 * @property-read \App\Service\TokenService             tokenService
 * @property-read \App\Service\TopicService             topicService
 * @property-read \App\Service\UserAvatarService        userAvatarService
 * @property-read \App\Service\UserCoverService         userCoverService
 * @property-read \App\Service\UserLoginService         userLoginService
 * @property-read \App\Service\UserPasswordResetService userPasswordResetService
 * @property-read \App\Service\UserRegisterService      userRegisterService
 * @property-read \App\Service\UserService              userService
 * @property-read \App\Service\VoteService              voteService
 *
 * @package App\Controller
 */
abstract class ControllerAbstracts
{
    use UrlTraits;

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
        $modules = [
            'App\\Service\\' . ucfirst($name),
            'App\\Library\\' . ucfirst($name),
            'request',
        ];

        foreach ($modules as $module) {
            if ($this->container->has($module)) {
                return $this->container->get($module);
            }
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
