<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 问答
 */
class Question extends ContainerAbstracts
{
    /**
     * 问答列表页
     *
     * @param  Request           $request
     * @param  Response          $response
     * @return ResponseInterface
     */
    public function pageIndex(Request $request, Response $response): ResponseInterface
    {
        return $this->view->render($response, '/question/index.php');
    }

    /**
     * 问答详情页
     *
     * @param  Request           $request
     * @param  Response          $response
     * @param  int               $question_id
     * @return ResponseInterface
     */
    public function pageInfo(Request $request, Response $response, int $question_id): ResponseInterface
    {
        return $this->view->render($response, '/question/info.php');
    }

    /**
     * 获取指定用户发表的提问列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getListByUserId(Request $request, Response $response, int $user_id): Response
    {
        return $this->questionService
            ->fetchCollection()
            ->getList(['user_id' => $user_id], true)
            ->render($response);
    }

    /**
     * 获取当前用户发表的提问列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMyList(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();

        return $this->questionService
            ->fetchCollection()
            ->getList(['user_id' => $userId], true)
            ->render($response);
    }

    /**
     * 根据话题ID获取提问列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function getListByTopicId(Request $request, Response $response, int $topic_id): Response
    {
        return $this->questionService
            ->fetchCollection()
            ->getList(['topic_id' => $topic_id], true)
            ->render($response);
    }

    /**
     * 获取问答列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        return $this->questionService
            ->fetchCollection()
            ->getList([], true)
            ->render($response);
    }

    /**
     * 创建提问
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $this->roleService->userIdOrFail();

        $questionId = $this->questionService->create(
            $request->getParsedBodyParam('title'),
            $request->getParsedBodyParam('content_markdown'),
            $request->getParsedBodyParam('content_rendered'),
            $this->requestService->getParsedBodyParamToArray('topic_id', 10)
        );

        return $this->questionService
            ->fetchCollection()
            ->get($questionId, true)
            ->render($response);
    }

    /**
     * 批量删除提问
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function deleteMultiple(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $questionIds = $this->requestService->getQueryParamToArray('question_id', 100);
        $this->questionService->deleteMultiple($questionIds);

        return collect()->render($response);
    }

    /**
     * 获取一个提问信息
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function getOne(Request $request, Response $response, int $question_id): Response
    {
        return $this->questionService
            ->fetchCollection()
            ->getOrFail($question_id, true)
            ->render($response);
    }

    /**
     * 更新提问
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function updateOne(Request $request, Response $response, int $question_id): Response
    {
        $title = $request->getParsedBodyParam('title');
        $contentMarkdown = $request->getParsedBodyParam('content_markdown');
        $contentRendered = $request->getParsedBodyParam('content_rendered');
        $topicIds = $this->requestService->getParsedBodyParamToArray('topic_id', 10);

        $this->questionService->update($question_id, $title, $contentMarkdown, $contentRendered, $topicIds);

        return $this->questionService
            ->fetchCollection()
            ->get($question_id, true)
            ->render($response);
    }

    /**
     * 删除一个提问
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function deleteOne(Request $request, Response $response, int $question_id): Response
    {
        $this->questionService->delete($question_id);

        return collect()->render($response);
    }

    /**
     * 获取指定提问下的评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function getComments(Request $request, Response $response, int $question_id): Response
    {
        return $this->questionService
            ->fetchCollection()
            ->getComments($question_id, true)
            ->render($response);
    }

    /**
     * 在指定提问下发表评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function addComment(Request $request, Response $response, int $question_id): Response
    {
        $content = $request->getParsedBodyParam('content');
        $commentId = $this->questionService->addComment($question_id, $content);

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
     * @param  int      $question_id
     * @return Response
     */
    public function getVoters(Request $request, Response $response, int $question_id): Response
    {
        $type = $request->getQueryParam('type');

        return $this->questionService
            ->fetchCollection()
            ->getVoters($question_id, $type, true)
            ->render($response);
    }

    /**
     * 添加投票
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function addVote(Request $request, Response $response, int $question_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $type = $request->getParsedBodyParam('type');

        $this->questionService->addVote($userId, $question_id, $type);
        $voteCount = $this->questionService->getVoteCount($question_id);

        return collect(['vote_count' => $voteCount])->render($response);
    }

    /**
     * 删除投票
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function deleteVote(Request $request, Response $response, int $question_id): Response
    {
        $userId = $this->roleService->userIdOrFail();

        $this->questionService->deleteVote($userId, $question_id);
        $voteCount = $this->questionService->getVoteCount($question_id);

        return collect(['vote_count' => $voteCount])->render($response);
    }

    /**
     * 获取指定用户关注的提问
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getFollowing(Request $request, Response $response, int $user_id): Response
    {
        return $this->questionService
            ->fetchCollection()
            ->getFollowing($user_id, true)
            ->render($response);
    }

    /**
     * 获取我关注的提问
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMyFollowing(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();

        return $this->questionService
            ->fetchCollection()
            ->getFollowing($userId, true)
            ->render($response);
    }

    /**
     * 获取指定提问的关注者
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function getFollowers(Request $request, Response $response, int $question_id): Response
    {
        return $this->questionService
            ->fetchCollection()
            ->getFollowers($question_id, true)
            ->render($response);
    }

    /**
     * 添加关注
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function addFollow(Request $request, Response $response, int $question_id): Response
    {
        $userId = $this->roleService->userIdOrFail();

        $this->questionService->addFollow($userId, $question_id);
        $followerCount = $this->questionService->getFollowerCount($question_id);

        return collect(['follower_count' => $followerCount])->render($response);
    }

    /**
     * 取消关注
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function deleteFollow(Request $request, Response $response, int $question_id): Response
    {
        $userId = $this->roleService->userIdOrFail();

        $this->questionService->deleteFollow($userId, $question_id);
        $followerCount = $this->questionService->getFollowerCount($question_id);

        return collect(['follower_count' => $followerCount])->render($response);
    }

    /**
     * 获取回收站中的提问列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getDeletedList(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        return $this->questionService
            ->fetchCollection()
            ->getList(['is_deleted' => true], true)
            ->render($response);
    }

    /**
     * 批量恢复提问
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
     * 批量删除回收站中的提问
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
     * 恢复指定提问
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function restoreOne(Request $request, Response $response, int $question_id): Response
    {
        return collect()->render($response);
    }

    /**
     * 删除回收站中的指定提问
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function destroyOne(Request $request, Response $response, int $question_id): Response
    {
        return collect()->render($response);
    }
}
