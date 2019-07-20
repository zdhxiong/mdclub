<?php

declare(strict_types=1);

namespace MDClub\Controller;

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
    public function pageIndex(): ResponseInterface
    {
        return $this->render('/user/index.php');
    }

    /**
     * 用户详情页
     *
     * @param  int      $user_id
     * @return ResponseInterface
     */
    public function pageInfo(int $user_id): ResponseInterface
    {
        return $this->render('/user/info.php');
    }
}
