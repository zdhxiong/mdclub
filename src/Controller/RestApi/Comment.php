<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\Abstracts;
use MDClub\Helper\Request;
use MDClub\Middleware\NeedManager;

/**
 * 评论
 */
class Comment extends Abstracts
{
    /**
     * 获取所有评论
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->commentGetService->getList();
    }

    /**
     * 批量删除评论
     *
     * @return array
     */
    public function deleteMultiple(): array
    {
        $commentIds = Request::getQueryParamToArray($this->request, 'comment_id', 100);

        $this->commentDeleteService->deleteMultiple($commentIds);

        return [];
    }

    /**
     * 获取评论详情
     *
     * @param  int      $comment_id
     * @return array
     */
    public function get(int $comment_id): array
    {
        return $this->commentGetService->getOrFail($comment_id);
    }

    /**
     * 更新评论
     *
     * @param  int      $comment_id
     * @return array
     */
    public function update(int $comment_id): array
    {
        $this->commentUpdateService->update(
            $comment_id,
            $this->request->getParsedBody()['content'] ?? null
        );

        return $this->commentGetService->get($comment_id);
    }

    /**
     * 删除评论
     *
     * @param  int      $comment_id
     * @return array
     */
    public function delete(int $comment_id): array
    {
        $this->commentDeleteService->delete($comment_id);

        return [];
    }

    /**
     * 获取投票者
     *
     * @param  int      $comment_id
     * @return array
     */
    public function getVoters(int $comment_id): array
    {
        $type = $this->request->getQueryParams()['type'] ?? null;

        return $this->commentVoteService->getVoters($comment_id, $type);
    }

    /**
     * 添加投票
     *
     * @param  int      $comment_id
     * @return array
     */
    public function addVote(int $comment_id): array
    {
        $type = $this->request->getParsedBody()['type'] ?? '';
        $this->commentVoteService->addVote($comment_id, $type);
        $voteCount = $this->commentVoteService->getVoteCount($comment_id);

        return ['vote_count' => $voteCount];
    }

    /**
     * 删除投票
     *
     * @param  int      $comment_id
     * @return array
     */
    public function deleteVote(int $comment_id): array
    {
        $this->commentVoteService->deleteVote($comment_id);
        $voteCount = $this->commentVoteService->getVoteCount($comment_id);

        return ['vote_count' => $voteCount];
    }

    /**
     * 获取回收站中的评论列表
     *
     * @uses NeedManager
     * @return array
     */
    public function getDeleted(): array
    {
        return $this->commentDeleteService->getDeleted();
    }

    /**
     * 批量恢复评论
     *
     * @uses NeedManager
     * @return array
     */
    public function restoreMultiple(): array
    {
        $commentIds = Request::getQueryParamToArray($this->request, 'comment_id', 100);

        $this->commentDeleteService->restoreMultiple($commentIds);

        return [];
    }

    /**
     * 批量删除回收站中的评论
     *
     * @uses NeedManager
     * @return array
     */
    public function destroyMultiple(): array
    {
        $commentIds = Request::getQueryParamToArray($this->request, 'comment_id', 100);

        $this->commentDeleteService->destroyMultiple($commentIds);

        return [];
    }

    /**
     * 恢复指定评论
     *
     * @uses NeedManager
     * @param  int      $comment_id
     * @return array
     */
    public function restore(int $comment_id): array
    {
        $this->commentDeleteService->restore($comment_id);

        return [];
    }

    /**
     * 删除指定评论
     *
     * @uses NeedManager
     * @param  int      $comment_id
     * @return array
     */
    public function destroy(int $comment_id): array
    {
        $this->answerDeleteService->destroy($comment_id);

        return [];
    }
}
