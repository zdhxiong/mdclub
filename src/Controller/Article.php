<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use App\Helper\ArrayHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 文章
 *
 * Class Article
 * @package App\Controller
 */
class Article extends ControllerAbstracts
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
        return $this->container->view->render($response, '/article/index.php');
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
        return $this->container->view->render($response, '/article/info.php');
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
        $list = $this->container->articleService->getList(['user_id' => $user_id], true);

        return $this->success($response, $list);
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
        $userId = $this->container->roleService->userIdOrFail();
        $list = $this->container->articleService->getList(['user_id' => $userId], true);

        return $this->success($response, $list);
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
        $list = $this->container->articleService->getList(['topic_id' => $topic_id], true);

        return $this->success($response, $list);
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
        $list = $this->container->articleService->getList([], true);

        return $this->success($response, $list);
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
        $this->container->roleService->userIdOrFail();

        $articleId = $this->container->articleService->create(
            $request->getParsedBodyParam('title'),
            $request->getParsedBodyParam('content_markdown'),
            $request->getParsedBodyParam('content_rendered'),
            ArrayHelper::getParsedBodyParam($request, 'topic_id', 10)
        );

        $articleInfo = $this->container->articleService->get($articleId, true);

        return $this->success($response, $articleInfo);
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
        $this->container->roleService->managerIdOrFail();

        $articleIds = ArrayHelper::getQueryParam($request, 'article_id', 100);
        $this->container->articleService->deleteMultiple($articleIds);

        return $this->success($response);
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
        $articleInfo = $this->container->articleService->getOrFail($article_id, true);

        return $this->success($response, $articleInfo);
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
        $topicIds = ArrayHelper::getParsedBodyParam($request, 'topic_id', 10);

        $this->container->articleService->update($article_id, $title, $contentMarkdown, $contentRendered, $topicIds);
        $articleInfo = $this->container->articleService->get($article_id, true);

        return $this->success($response, $articleInfo);
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
        $this->container->articleService->delete($article_id);

        return $this->success($response);
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
        $list = $this->container->articleService->getComments($article_id, true);

        return $this->success($response, $list);
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
        $commentId = $this->container->articleService->addComment($article_id, $content);
        $comment = $this->container->commentService->get($commentId, true);

        return $this->success($response, $comment);
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
        $voters = $this->container->articleService->getVoters($article_id, $type, true);

        return $this->success($response, $voters);
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
        $userId = $this->container->roleService->userIdOrFail();
        $type = $request->getParsedBodyParam('type');

        $this->container->articleService->addVote($userId, $article_id, $type);
        $voteCount = $this->container->articleService->getVoteCount($article_id);

        return $this->success($response, ['vote_count' => $voteCount]);
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
        $userId = $this->container->roleService->userIdOrFail();

        $this->container->articleService->deleteVote($userId, $article_id);
        $voteCount = $this->container->articleService->getVoteCount($article_id);

        return $this->success($response, ['vote_count' => $voteCount]);
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
        $following = $this->container->articleService->getFollowing($user_id, true);

        return $this->success($response, $following);
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
        $userId = $this->container->roleService->userIdOrFail();
        $following = $this->container->articleService->getFollowing($userId, true);

        return $this->success($response, $following);
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
        $followers = $this->container->articleService->getFollowers($article_id, true);

        return $this->success($response, $followers);
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
        $userId = $this->container->roleService->userIdOrFail();

        $this->container->articleService->addFollow($userId, $article_id);
        $followerCount = $this->container->articleService->getFollowerCount($article_id);

        return $this->success($response, ['follower_count' => $followerCount]);
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
        $userId = $this->container->roleService->userIdOrFail();

        $this->container->articleService->deleteFollow($userId, $article_id);
        $followerCount = $this->container->articleService->getFollowerCount($article_id);

        return $this->success($response, ['follower_count' => $followerCount]);
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
        $this->container->roleService->managerIdOrFail();

        $list = $this->container->articleService->getList(['is_deleted' => true], true);

        return $this->success($response, $list);
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
        return $response;
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
        return $response;
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
        return $response;
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
        return $response;
    }
}
