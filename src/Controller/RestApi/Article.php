<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\Abstracts;
use MDClub\Helper\Request;

/**
 * 文章
 */
class Article extends Abstracts
{
    /**
     * 获取文章列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->articleGetService->getList();
    }

    /**
     * 发表文章
     *
     * @return array
     */
    public function create(): array
    {
        $articleId = $this->articleCreateService->create(
            $this->request->getParsedBody()['title'] ?? '',
            $this->request->getParsedBody()['content_markdown'] ?? '',
            $this->request->getParsedBody()['content_rendered'] ?? '',
            Request::getBodyParamToArray($this->request, 'topic_id', 10)
        );

        return $this->articleGetService->get($articleId);
    }

    /**
     * 批量删除文章
     *
     * @return array
     */
    public function deleteMultiple(): array
    {
        $articleIds = Request::getQueryParamToArray($this->request, 'article_id', 100);

        $this->articleDeleteService->deleteMultiple($articleIds);

        return [];
    }

    /**
     * 获取指定文章详情
     *
     * @param  int   $article_id
     * @return array
     */
    public function get(int $article_id): array
    {
        return $this->articleGetService->getOrFail($article_id);
    }

    /**
     * 更新指定文章
     *
     * @param  int   $article_id
     * @return array
     */
    public function update(int $article_id): array
    {
        $this->articleUpdateService->update(
            $article_id,
            $this->request->getParsedBody()['title'] ?? null,
            $this->request->getParsedBody()['content_markdown'] ?? null,
            $this->request->getParsedBody()['content_rendered'] ?? null,
            Request::getBodyParamToArray($this->request, 'topic_id', 10)
        );

        return $this->articleGetService->get($article_id);
    }

    /**
     * 删除指定文章
     *
     * @param  int   $article_id
     * @return array
     */
    public function delete(int $article_id): array
    {
        $this->articleDeleteService->delete($article_id);

        return [];
    }

    /**
     * 获取投票者
     *
     * @param  int   $article_id
     * @return array
     */
    public function getVoters(int $article_id): array
    {
        $type = $this->request->getQueryParams()['type'] ?? null;

        return $this->articleVoteService->getVoters($article_id, $type);
    }

    /**
     * 添加投票
     *
     * @param  int   $article_id
     * @return array
     */
    public function addVote(int $article_id): array
    {
        $type = $this->request->getParsedBody()['type'] ?? '';
        $this->articleVoteService->addVote($article_id, $type);
        $voteCount = $this->articleVoteService->getVoteCount($article_id);

        return ['vote_count' => $voteCount];
    }

    /**
     * 删除投票
     *
     * @param  int   $article_id
     * @return array
     */
    public function deleteVote(int $article_id): array
    {
        $this->articleVoteService->deleteVote($article_id);
        $voteCount = $this->articleVoteService->getVoteCount($article_id);

        return ['vote_count' => $voteCount];
    }

    /**
     * 获取指定文章的关注者
     *
     * @param  int   $article_id
     * @return array
     */
    public function getFollowers(int $article_id): array
    {
        return $this->articleFollowService->getFollowers($article_id);
    }

    /**
     * 添加关注
     *
     * @param  int   $article_id
     * @return array
     */
    public function addFollow(int $article_id): array
    {
        $this->articleFollowService->addFollow($article_id);
        $followerCount = $this->articleFollowService->getFollowerCount($article_id);

        return ['follower_count' => $followerCount];
    }

    /**
     * 取消关注
     *
     * @param  int   $article_id
     * @return array
     */
    public function deleteFollow(int $article_id): array
    {
        $this->articleFollowService->deleteFollow($article_id);
        $followerCount = $this->articleFollowService->getFollowerCount($article_id);

        return ['follower_count' => $followerCount];
    }

    /**
     * 获取指定文章下的评论列表
     *
     * @param  int   $article_id
     * @return array
     */
    public function getComments(int $article_id): array
    {
        return $this->articleCommentService->getComments($article_id);
    }

    /**
     * 在指定文章下发表评论
     *
     * @param  int   $article_id
     * @return array
     */
    public function createComment(int $article_id): array
    {
        $content = $this->request->getParsedBody()['content'] ?? '';
        $commentId = $this->articleCommentService->addComment($article_id, $content);

        return $this->commentGetService->get($commentId);
    }

    /**
     * 获取回收站中的文章列表
     *
     * @uses NeedManager
     * @return array
     */
    public function getDeleted(): array
    {
        return $this->articleDeleteService->getDeleted();
    }

    /**
     * 批量恢复文章
     *
     * @uses NeedManager
     * @return array
     */
    public function restoreMultiple(): array
    {
        $articleIds = Request::getQueryParamToArray($this->request, 'article_id', 100);

        $this->articleDeleteService->restoreMultiple($articleIds);

        return [];
    }

    /**
     * 批量删除回收站中的文章
     *
     * @uses NeedManager
     * @return array
     */
    public function destroyMultiple(): array
    {
        $articleIds = Request::getQueryParamToArray($this->request, 'article_id', 100);

        $this->articleDeleteService->destroyMultiple($articleIds);

        return [];
    }

    /**
     * 恢复指定文章
     *
     * @uses NeedManager
     * @param  int   $article_id
     * @return array
     */
    public function restore(int $article_id): array
    {
        $this->articleDeleteService->restore($article_id);

        return [];
    }

    /**
     * 删除指定文章
     *
     * @uses NeedManager
     * @param  int      $article_id
     * @return array
     */
    public function destroy(int $article_id): array
    {
        $this->articleDeleteService->destroy($article_id);

        return [];
    }
}
