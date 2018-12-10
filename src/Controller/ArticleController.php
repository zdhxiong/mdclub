<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 文章
 *
 * Class ArticleController
 * @package App\Controller
 */
class ArticleController extends ControllerAbstracts
{
    /**
     * 文章列表页面
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function pageIndex(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 文章详情页
     *
     * @param Request $request
     * @param Response $response
     * @param int $article_id
     *
     * @return Response
     */
    public function pageDetail(Request $request, Response $response, int $article_id): Response
    {
        return $response;
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
        $list = $this->articleService->getListByUserId($user_id, true);

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
        $userId = $this->roleService->userIdOrFail();
        $list = $this->articleService->getListByUserId($userId, true);

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
        $list = $this->articleService->getList(true);

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
        $userId = $this->roleService->userIdOrFail();

        $articleId = $this->articleService->create(
            $userId,
            $request->getParsedBodyParam('title'),
            $request->getParsedBodyParam('content_markdown'),
            $request->getParsedBodyParam('content_rendered'),
            array_unique(array_filter(explode(',', $request->getParsedBodyParam('topic_id'))))
        );

        $articleInfo = $this->articleService->get($articleId, true);

        return $this->success($response, $articleInfo);
    }

    /**
     * 获取指定文章详情
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $article_id
     * @return Response
     */
    public function get(Request $request, Response $response, int $article_id): Response
    {
        $articleInfo = $this->articleService->get($article_id, true);

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
    public function update(Request $request, Response $response, int $article_id): Response
    {
        $title = $request->getParsedBodyParam('title');
        $contentMarkdown = $request->getParsedBodyParam('content_markdown');
        $contentRendered = $request->getParsedBodyParam('content_rendered');
        $topicIds = $request->getParsedBodyParam('topic_id');

        if ($topicIds) {
            $topicIds = array_unique(array_filter(explode(',', $topicIds)));
        }

        $this->articleService->update($article_id, $title, $contentMarkdown, $contentRendered, $topicIds);
        $articleInfo = $this->articleService->get($article_id, true);

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
    public function delete(Request $request, Response $response, int $article_id): Response
    {
        $this->articleService->delete($article_id);

        return $this->success($response);
    }

    /**
     * 批量删除文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function batchDelete(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $articleIds = $request->getQueryParam('article_id');

        if ($articleIds) {
            $articleIds = array_unique(array_filter(array_slice(explode(',', $articleIds), 0, 100)));
        }

        if ($articleIds) {
            $this->articleService->batchDelete($articleIds);
        }

        return $this->success($response);
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
        $following = $this->articleService->getFollowing($user_id, true);

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
        $userId = $this->roleService->userIdOrFail();
        $following = $this->articleService->getFollowing($userId, true);

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
        $followers = $this->articleService->getFollowers($article_id, true);

        return $this->success($response, $followers);
    }

    /**
     * 当前用户关注指定文章
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

        return $this->success($response, ['follower_count' => $followerCount]);
    }

    /**
     * 当前用户取消关注指定文章
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

        return $this->success($response, ['follower_count' => $followerCount]);
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
        $userId = $this->roleService->userIdOrFail();
        $this->articleService->deleteVote($userId, $article_id);
        $voteCount = $this->articleService->getVoteCount($article_id);

        return $this->success($response, ['vote_count' => $voteCount]);
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
        $voters = $this->articleService->getVoters($article_id, $type, true);

        return $this->success($response, $voters);
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
        $list = $this->articleService->getComments($article_id, true);

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
        $commentId = $this->articleService->addComment($article_id, $content);
        $comment = $this->commentService->get($commentId, true);

        return $this->success($response, $comment);
    }

    /**
     * 获取回收站中的文章列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getDeleted(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 批量恢复文章
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function batchRestore(Request $request, Response $response): Response
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
    public function batchDestroy(Request $request, Response $response): Response
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
    public function restore(Request $request, Response $response, int $article_id): Response
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
    public function destroy(Request $request, Response $response, int $article_id): Response
    {
        return $response;
    }
}
