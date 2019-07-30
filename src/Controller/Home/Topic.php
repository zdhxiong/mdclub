<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Controller\Abstracts;
use Psr\Http\Message\ResponseInterface;

/**
 * 话题
 */
class Topic extends Abstracts
{
    /**
     * 话题列表页
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return $this->render('/topic/index.php');
    }

    /**
     * 话题详情页
     *
     * @param  int      $topic_id
     * @return ResponseInterface
     */
    public function info(int $topic_id): ResponseInterface
    {
        return $this->render('/topic/info.php');
    }
}
