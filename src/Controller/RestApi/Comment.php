<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\RestApi\Traits\Deletable;
use MDClub\Controller\RestApi\Traits\Getable;
use MDClub\Controller\RestApi\Traits\Votable;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Service\CommentService;

/**
 * 评论 API
 */
class Comment extends Abstracts
{
    use Deletable;
    use Getable;
    use Votable;

    /**
     * @inheritDoc
     */
    protected function getService(): string
    {
        return \MDClub\Service\Comment::class;
    }

    /**
     * 更新评论
     *
     * @param int $commentId
     *
     * @return array
     */
    public function update(int $commentId): array
    {
        $requestBody = Request::getParsedBody();
        CommentService::update($commentId, $requestBody);

        return CommentService::get($commentId);
    }
}
