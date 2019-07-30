<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Controller\Abstracts;
use Psr\Http\Message\ResponseInterface;

/**
 * 私信
 */
class Inbox extends Abstracts
{
    /**
     * 私信列表页
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->render('/inbox/index.php');
    }
}
