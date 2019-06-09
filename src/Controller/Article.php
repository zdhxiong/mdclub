<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 文章
 */
class Article extends ContainerAbstracts
{
    /**
     * 文章列表页面
     *
     * @param  Request           $request
     * @param  Response          $response
     * @return ResponseInterface
     */
    public function pageIndex(Request $request, Response $response): ResponseInterface
    {
        return $this->view->render($response, '/article/index.php');
    }

    /**
     * 文章详情页面
     *
     * @param  Request           $request
     * @param  Response          $response
     * @param  int               $article_id
     * @return ResponseInterface
     */
    public function pageInfo(Request $request, Response $response, int $article_id): ResponseInterface
    {
        return $this->view->render($response, '/article/info.php');
    }

    /**
     * 获取指定用户发表的文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getListByUserId(Request $request, Response $response, int $user_id): Response
    {
        return $this->articleService
            ->fetchCollection()
            ->getList(['user_id' => $user_id], true)
            ->render($response);
    }

    /**
     * 获取当前用户发表的文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMyList(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();

        return $this->articleService
            ->fetchCollection()
            ->getList(['user_id' => $userId], true)
            ->render($response);
    }

    /**
     * 根据话题ID获取文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function getListByTopicId(Request $request, Response $response, int $topic_id): Response
    {
        return $this->articleService
            ->fetchCollection()
            ->getList(['topic_id' => $topic_id], true)
            ->render($response);
    }

    /**
     * 获取文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        return $this->articleService
            ->fetchCollection()
            ->getList([], true)
            ->render($response);
    }

    /**
     * 发表文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $this->roleService->userIdOrFail();

        $articleId = $this->articleService->create(
            $request->getParsedBodyParam('title'),
            $request->getParsedBodyParam('content_markdown'),
            $request->getParsedBodyParam('content_rendered'),
            $this->requestService->getParsedBodyParamToArray('topic_id', 10)
        );

        return $this->articleService
            ->fetchCollection()
            ->get($articleId, true)
            ->render($response);
    }

    /**
     * 批量删除文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function deleteMultiple(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $articleIds = $this->requestService->getQueryParamToArray('article_id', 100);
        $this->articleService->deleteMultiple($articleIds);

        return collect()->render($response);
    }

    /**
     * 获取指定文章详情
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function getOne(Request $request, Response $response, int $article_id): Response
    {
        return $this->articleService
            ->fetchCollection()
            ->getOrFail($article_id, true)
            ->render($response);
    }

    /**
     * 更新指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function updateOne(Request $request, Response $response, int $article_id): Response
    {
        $title = $request->getParsedBodyParam('title');
        $contentMarkdown = $request->getParsedBodyParam('content_markdown');
        $contentRendered = $request->getParsedBodyParam('content_rendered');
        $topicIds = $this->requestService->getParsedBodyParamToArray('topic_id', 10);

        $this->articleService->update($article_id, $title, $contentMarkdown, $contentRendered, $topicIds);

        return $this->articleService
            ->fetchCollection()
            ->get($article_id, true)
            ->render($response);
    }

    /**
     * 删除指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function deleteOne(Request $request, Response $response, int $article_id): Response
    {
        $this->articleService->delete($article_id);

        return collect()->render($response);
    }

    /**
     * 获取指定文章下的评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function getComments(Request $request, Response $response, int $article_id): Response
    {
        return $this->articleService
            ->fetchCollection()
            ->getComments($article_id, true)
            ->render($response);
    }

    /**
     * 在指定文章下发表评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function addComment(Request $request, Response $response, int $article_id): Response
    {
        $content = $request->getParsedBodyParam('content');
        $commentId = $this->articleService->addComment($article_id, $content);

        return $this->commentService
            ->fetchCollection()
            ->get($commentId, true)
            ->render($response);
    }

    /**
     * 获取投票者
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function getVoters(Request $request, Response $response, int $article_id): Response
    {
        $type = $request->getQueryParam('type');

        return $this->articleService
            ->fetchCollection()
            ->getVoters($article_id, $type, true)
            ->render($response);
    }

    /**
     * 添加投票
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function addVote(Request $request, Response $response, int $article_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $type = $request->getParsedBodyParam('type');

        $this->articleService->addVote($userId, $article_id, $type);
        $voteCount = $this->articleService->getVoteCount($article_id);

        return collect(['vote_count' => $voteCount])->render($response);
    }

    /**
     * 删除投票
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function deleteVote(Request $request, Response $response, int $article_id): Response
    {
        $userId = $this->roleService->userIdOrFail();

        $this->articleService->deleteVote($userId, $article_id);
        $voteCount = $this->articleService->getVoteCount($article_id);

        return collect(['vote_count' => $voteCount])->render($response);
    }

    /**
     * 获取指定用户关注的文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getFollowing(Request $request, Response $response, int $user_id): Response
    {
        return $this->articleService
            ->fetchCollection()
            ->getFollowing($user_id, true)
            ->render($response);
    }

    /**
     * 获取当前用户关注的文章列表
     *
     * @param  Request $request
     * @param  Response $response
     * @return Response
     */
    public function getMyFollowing(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();

        return $this->articleService
            ->fetchCollection()
            ->getFollowing($userId, true)
            ->render($response);
    }

    /**
     * 获取指定文章的关注者
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function getFollowers(Request $request, Response $response, int $article_id): Response
    {
        return $this->articleService
            ->fetchCollection()
            ->getFollowers($article_id, true)
            ->render($response);
    }

    /**
     * 添加关注
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function addFollow(Request $request, Response $response, int $article_id): Response
    {
        $userId = $this->roleService->userIdOrFail();

        $this->articleService->addFollow($userId, $article_id);
        $followerCount = $this->articleService->getFollowerCount($article_id);

        return collect(['follower_count' => $followerCount])->render($response);
    }

    /**
     * 取消关注
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function deleteFollow(Request $request, Response $response, int $article_id): Response
    {
        $userId = $this->roleService->userIdOrFail();

        $this->articleService->deleteFollow($userId, $article_id);
        $followerCount = $this->articleService->getFollowerCount($article_id);

        return collect(['follower_count' => $followerCount])->render($response);
    }

    /**
     * 获取回收站中的文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getDeletedList(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        return $this->articleService
            ->fetchCollection()
            ->getList(['is_deleted' => true], true)
            ->render($response);
    }

    /**
     * 批量恢复文章
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
     * 批量删除回收站中的文章
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
     * 恢复指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function restoreOne(Request $request, Response $response, int $article_id): Response
    {
        return collect()->render($response);
    }

    /**
     * 删除指定文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function destroyOne(Request $request, Response $response, int $article_id): Response
    {
        return collect()->render($response);
    }
}
