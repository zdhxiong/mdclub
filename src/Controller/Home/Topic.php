<?php

declare(strict_types=1);

namespace MDClub\Controller\Home;

use MDClub\Facade\Library\View;
use MDClub\Facade\Service\TopicService;
use Psr\Http\Message\ResponseInterface;

/**
 * 话题页面
 */
class Topic
{
    /**
     * 话题列表页
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        return View::render('/topic/index.php');
    }

    /**
     * 话题详情页
     *
     * @param int $topicId
     *
     * @return ResponseInterface
     */
    public function info(int $topicId): ResponseInterface
    {
        return View::render(
            '/topic/info.php',
            [
                'topic' => TopicService::getOrFail($topicId),
            ]
        );
    }
}
