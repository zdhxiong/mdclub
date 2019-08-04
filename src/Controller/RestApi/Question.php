<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\Abstracts;
use MDClub\Helper\Request;

/**
 * 提问 restful api
 */
class Question extends Abstracts
{
    /**
     * 获取提问列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->questionGetService->getList();
    }

    /**
     * 创建提问
     *
     * @return array
     */
    public function create(): array
    {
        $questionId = $this->questionCreateService->create(
            $this->request->getParsedBody()['title'] ?? '',
            $this->request->getParsedBody()['content_markdown'] ?? '',
            $this->request->getParsedBody()['content_rendered'] ?? '',
            Request::getBodyParamToArray($this->request, 'topic_id', 10)
        );

        return $this->questionGetService->get($questionId);
    }

    /**
     * 批量删除提问
     *
     * @return array
     */
    public function deleteMultiple(): array
    {
        $questionIds = Request::getQueryParamToArray($this->request, 'question_id', 100) ?? [];

        $this->questionDeleteService->deleteMultiple($questionIds);

        return [];
    }

    /**
     * 获取一个提问信息
     *
     * @param  int      $question_id
     * @return array
     */
    public function get(int $question_id): array
    {
        return $this->questionGetService->getOrFail($question_id);
    }

    /**
     * 更新提问
     *
     * @param  int      $question_id
     * @return array
     */
    public function update(int $question_id): array
    {
        $this->questionUpdateService->update(
            $question_id,
            $this->request->getParsedBody()['title'] ?? null,
            $this->request->getParsedBody()['content_markdown'] ?? null,
            $this->request->getParsedBody()['content_rendered'] ?? null,
            Request::getBodyParamToArray($this->request, 'topic_id', 10)
        );

        return $this->questionGetService->get($question_id);
    }

    /**
     * 删除一个提问
     *
     * @param  int      $question_id
     * @return array
     */
    public function delete(int $question_id): array
    {
        $this->questionDeleteService->delete($question_id);

        return [];
    }

    /**
     * 获取投票者
     *
     * @param  int      $question_id
     * @return array
     */
    public function getVoters(int $question_id): array
    {
        $type = $this->request->getQueryParams()['type'] ?? null;

        return $this->questionVoteService->getVoters($question_id, $type);
    }

    /**
     * 添加投票
     *
     * @param  int      $question_id
     * @return array
     */
    public function addVote(int $question_id): array
    {
        $type = $this->request->getParsedBody()['type'] ?? '';
        $this->questionVoteService->addVote($question_id, $type);
        $voteCount = $this->questionVoteService->getVoteCount($question_id);

        return ['vote_count' => $voteCount];
    }

    /**
     * 删除投票
     *
     * @param  int      $question_id
     * @return array
     */
    public function deleteVote(int $question_id): array
    {
        $this->questionVoteService->deleteVote($question_id);
        $voteCount = $this->questionVoteService->getVoteCount($question_id);

        return ['vote_count' => $voteCount];
    }

    /**
     * 获取指定提问的关注者
     *
     * @param  int      $question_id
     * @return array
     */
    public function getFollowers(int $question_id): array
    {
        return $this->questionFollowService->getFollowers($question_id);
    }

    /**
     * 添加关注
     *
     * @param  int      $question_id
     * @return array
     */
    public function addFollow(int $question_id): array
    {
        $this->questionFollowService->addFollow($question_id);
        $followerCount = $this->questionFollowService->getFollowerCount($question_id);

        return ['follower_count' => $followerCount];
    }

    /**
     * 取消关注
     *
     * @param  int      $question_id
     * @return array
     */
    public function deleteFollow(int $question_id): array
    {
        $this->questionFollowService->deleteFollow($question_id);
        $followerCount = $this->questionFollowService->getFollowerCount($question_id);

        return ['follower_count' => $followerCount];
    }

    /**
     * 获取指定提问下的评论列表
     *
     * @param  int    $question_id
     * @return array
     */
    public function getComments(int $question_id): array
    {
        return $this->questionCommentService->getComments($question_id);
    }

    /**
     * 在指定提问下发表评论
     *
     * @param  int      $question_id
     * @return array
     */
    public function createComment(int $question_id): array
    {
        $content = $this->request->getParsedBody()['content'] ?? '';
        $commentId = $this->questionCommentService->addComment($question_id, $content);

        return $this->commentGetService->get($commentId);
    }

    /**
     * 获取指定提问下的回答列表
     *
     * @param  int   $question_id
     * @return array
     */
    public function getAnswers(int $question_id): array
    {
        return $this->answerGetService->getByQuestionId($question_id);
    }

    /**
     * 在指定提问下创建回答
     *
     * @param  int   $question_id
     * @return array
     */
    public function createAnswer(int $question_id): array
    {
        $answerId = $this->answerCreateService->create(
            $question_id,
            $this->request->getParsedBody()['content_markdown'] ?? '',
            $this->request->getParsedBody()['content_rendered'] ?? ''
        );

        return $this->answerGetService->get($answerId);
    }

    /**
     * 获取回收站中的提问列表
     *
     * @uses NeedManager
     * @return array
     */
    public function getDeleted(): array
    {
        return $this->questionDeleteService->getDeleted();
    }

    /**
     * 批量恢复提问
     *
     * @uses NeedManager
     * @return array
     */
    public function restoreMultiple(): array
    {
        $questionIds = Request::getQueryParamToArray($this->request, 'question_id', 100);

        $this->questionDeleteService->restoreMultiple($questionIds);

        return [];
    }

    /**
     * 批量删除回收站中的提问
     *
     * @uses NeedManager
     * @return array
     */
    public function destroyMultiple(): array
    {
        $questionIds = Request::getQueryParamToArray($this->request, 'question_id', 100);

        $this->questionDeleteService->destroyMultiple($questionIds);

        return [];
    }

    /**
     * 恢复指定提问
     *
     * @uses NeedManager
     * @param  int      $question_id
     * @return array
     */
    public function restore(int $question_id): array
    {
        $this->questionDeleteService->restore($question_id);

        return [];
    }

    /**
     * 删除回收站中的指定提问
     *
     * @uses NeedManager
     * @param  int      $question_id
     * @return array
     */
    public function destroy(int $question_id): array
    {
        $this->questionDeleteService->destroy($question_id);

        return [];
    }
}
