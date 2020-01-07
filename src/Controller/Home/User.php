<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Facade\Library\View;
use MDClub\Facade\Service\UserService;
use Psr\Http\Message\ResponseInterface;

/**
 * 用户页面
 */
class User
{
    /**
     * 用户列表页
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return View::render('/user/index.php');
    }

    /**
     * 用户详情页
     *
     * @param int $userId
     *
     * @return ResponseInterface
     */
    public function info(int $userId): ResponseInterface
    {
        return View::render(
            '/user/info.php',
            [
                'user' => UserService::getOrFail($userId),
            ]
        );
    }
}
