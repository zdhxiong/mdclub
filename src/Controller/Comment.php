<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 评论
 */
class Comment extends ContainerAbstracts
{
    /**
     * 获取指定用户发表的评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getListByUserId(Request $request, Response $response, int $user_id): Response
    {
        return $this->commentGetService
            ->forApi()
            ->getByUserId($user_id)
            ->render($response);
    }

    /**
     * 获取当前用户发表的评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMyList(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();

        return $this->commentGetService
            ->forApi()
            ->getByUserId($userId)
            ->render($response);
    }

    /**
     * 获取所有评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        return $this->commentGetService
            ->forApi()
            ->getList()
            ->render($response);
    }

    /**
     * 批量删除评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function deleteMultiple(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $commentIds = $this->requestService->getQueryParamToArray('comment_id', 100);
        $this->commentService->deleteMultiple($commentIds);

        return collect()->render($response);
    }

    /**
     * 获取评论详情
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function getOne(Request $request, Response $response, int $comment_id): Response
    {
        return $this->commentGetService
            ->forApi()
            ->getOrFail($comment_id)
            ->render($response);
    }

    /**
     * 更新评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function updateOne(Request $request, Response $response, int $comment_id): Response
    {
        $content = $request->getParsedBodyParam('content');

        $this->commentUpdateService->update($comment_id, $content);

        return $this->commentGetService
            ->forApi()
            ->get($comment_id)
            ->render($response);
    }

    /**
     * 删除评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function deleteOne(Request $request, Response $response, int $comment_id): Response
    {
        $this->commentService->delete($comment_id);

        return collect()->render($response);
    }

    /**
     * 获取投票者
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function getVoters(Request $request, Response $response, int $comment_id): Response
    {
        $type = $request->getQueryParam('type');

        return $this->commentService
            ->fetchCollection()
            ->getVoters($comment_id, $type, true)
            ->render($response);
    }

    /**
     * 添加投票
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function addVote(Request $request, Response $response, int $comment_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $type = $request->getParsedBodyParam('type');

        $this->commentService->addVote($userId, $comment_id, $type);
        $voteCount = $this->commentService->getVoteCount($comment_id);

        return collect(['vote_count' => $voteCount])->render($response);
    }

    /**
     * 删除投票
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function deleteVote(Request $request, Response $response, int $comment_id): Response
    {
        $userId = $this->roleService->userIdOrFail();

        $this->commentService->deleteVote($userId, $comment_id);
        $voteCount = $this->commentService->getVoteCount($comment_id);

        return collect(['vote_count' => $voteCount])->render($response);
    }

    /**
     * 获取回收站中的评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getDeletedList(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        return $this->commentGetService
            ->forApi()
            ->getDeleted()
            ->render($response);
    }

    /**
     * 批量恢复评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function restoreMultiple(Request $request, Response $response): Response
    {
        return collect()->render($response);
    }

    /**
     * 批量删除回收站中的评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function destroyMultiple(Request $request, Response $response): Response
    {
        return collect()->render($response);
    }

    /**
     * 恢复指定评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function restoreOne(Request $request, Response $response, int $comment_id): Response
    {
        return collect()->render($response);
    }

    /**
     * 删除指定评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function destroyOne(Request $request, Response $response, int $comment_id): Response
    {
        return collect()->render($response);
    }
}
