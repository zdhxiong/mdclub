<?php

declare(strict_types=1);

namespace MDClub\Controller;

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
    public function pageIndex(): ResponseInterface
    {
        return $this->render('/notification/index.php');
    }
}
