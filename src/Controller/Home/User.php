<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Controller\Abstracts;
use Psr\Http\Message\ResponseInterface;

/**
 * 用户
 */
class User extends Abstracts
{
    /**
     * 用户列表页
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->render('/user/index.php');
    }

    /**
     * 用户详情页
     *
     * @param  int      $user_id
     * @return ResponseInterface
     */
    public function info(int $user_id): ResponseInterface
    {
        return $this->render('/user/info.php');
    }
}
