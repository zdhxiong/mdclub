<?php

declare(strict_types=1);

namespace MDClub\Controller;

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
    public function pageIndex(): ResponseInterface
    {
        return $this->render('/inbox/index.php');
    }
}
