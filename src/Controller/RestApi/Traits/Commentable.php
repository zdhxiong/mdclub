<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi\Traits;

use MDClub\Facade\Library\Request;
use MDClub\Facade\Service\CommentService;
use MDClub\Service\Interfaces\CommentableInterface;

/**
 * 可评论。用于 question, answer, article
 */
trait Commentable
{
    /**
     * @inheritDoc
     *
     * @return CommentableInterface
     */
    abstract protected function getServiceInstance();

    /**
     * 获取评论列表
     *
     * @param int $commentableId
     *
     * @return array
     */
    public function getComments(int $commentableId): array
    {
        $service = $this->getServiceInstance();

        return $service->getComments($commentableId);
    }

    /**
     * 发表评论
     *
     * @param int $commentableId
     *
     * @return array
     */
    public function createComment(int $commentableId): array
    {
        $service = $this->getServiceInstance();
        $requestBody = Request::getParsedBody();

        $commentId = $service->addComment($commentableId, $requestBody);

        return CommentService::get($commentId);
    }
}
