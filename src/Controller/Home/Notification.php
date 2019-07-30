<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Controller\Abstracts;
use Psr\Http\Message\ResponseInterface;

/**
 * 通知
 */
class Notification extends Abstracts
{
    /**
     * 通知列表页
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->render('/notification/index.php');
    }
}
