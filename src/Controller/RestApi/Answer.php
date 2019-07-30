<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\Abstracts;
use MDClub\Helper\Request;
use MDClub\Middleware\NeedManager;

/**
 * 回答
 */
class Answer extends Abstracts
{
    /**
     * 获取回答列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->answerGetService->getList();
    }

    /**
     * 批量删除回答
     *
     * @return array
     */
    public function deleteMultiple(): array
    {
        $answerIds = Request::getQueryParamToArray($this->request, 'answer_id', 100);

        $this->answerDeleteService->deleteMultiple($answerIds);

        return [];
    }

    /**
     * 获取指定回答的详情
     *
     * @param  int   $answer_id
     * @return array
     */
    public function get(int $answer_id): array
    {
        return $this->answerGetService->getOrFail($answer_id);
    }

    /**
     * 更新指定回答
     *
     * @param  int   $answer_id
     * @return array
     */
    public function update(int $answer_id): array
    {
        $this->answerUpdateService->update(
            $answer_id,
            $this->request->getParsedBody()['content_markdown'] ?? null,
            $this->request->getParsedBody()['content_rendered'] ?? null
        );

        return $this->answerGetService->get($answer_id);
    }

    /**
     * 删除指定回答
     *
     * @param  int      $answer_id
     * @return array
     */
    public function delete(int $answer_id): array
    {
        $this->answerDeleteService->delete($answer_id);

        return [];
    }

    /**
     * 获取投票者
     *
     * @param  int   $answer_id
     * @return array
     */
    public function getVoters(int $answer_id): array
    {
        $type = $this->request->getQueryParams()['type'] ?? null;

        return $this->answerVoteService->getVoters($answer_id, $type);
    }

    /**
     * 添加投票
     *
     * @param  int   $answer_id
     * @return array
     */
    public function addVote(int $answer_id): array
    {
        $type = $this->request->getParsedBody()['type'] ?? '';
        $this->answerVoteService->addVote($answer_id, $type);
        $voteCount = $this->answerVoteService->getVoteCount($answer_id);

        return ['vote_count' => $voteCount];
    }

    /**
     * 删除投票
     *
     * @param  int   $answer_id
     * @return array
     */
    public function deleteVote(int $answer_id): array
    {
        $this->answerVoteService->deleteVote($answer_id);
        $voteCount = $this->answerVoteService->getVoteCount($answer_id);

        return ['vote_count' => $voteCount];
    }

    /**
     * 获取指定回答下的评论列表
     *
     * @param  int    $answer_id
     * @return array
     */
    public function getComments(int $answer_id): array
    {
        return $this->answerCommentService->getComments($answer_id);
    }

    /**
     * 在指定回答下发表评论
     *
     * @param  int   $answer_id
     * @return array
     */
    public function createComment(int $answer_id): array
    {
        $content = $this->request->getParsedBody()['content'] ?? '';
        $commentId = $this->answerCommentService->addComment($answer_id, $content);

        return $this->commentGetService->get($commentId);
    }

    /**
     * 获取回收站中的回答列表
     *
     * @uses NeedManager
     * @return array
     */
    public function getDeleted(): array
    {
        return $this->answerDeleteService->getDeleted();
    }

    /**
     * 批量恢复回答
     *
     * @uses NeedManager
     * @return array
     */
    public function restoreMultiple(): array
    {
        $answerIds = Request::getQueryParamToArray($this->request, 'answer_id', 100);

        $this->answerDeleteService->restoreMultiple($answerIds);

        return [];
    }

    /**
     * 批量删除回收站中的回答
     *
     * @uses NeedManager
     * @return array
     */
    public function destroyMultiple(): array
    {
        $answerIds = Request::getQueryParamToArray($this->request, 'answer_id', 100);

        $this->answerDeleteService->destroyMultiple($answerIds);

        return [];
    }

    /**
     * 恢复指定回答
     *
     * @uses NeedManager
     * @param  int   $answer_id
     * @return array
     */
    public function restore(int $answer_id): array
    {
        $this->answerDeleteService->restore($answer_id);

        return [];
    }

    /**
     * 删除指定回答
     *
     * @uses NeedManager
     * @param  int   $answer_id
     * @return array
     */
    public function destroy(int $answer_id): array
    {
        $this->answerDeleteService->destroy($answer_id);

        return [];
    }
}
