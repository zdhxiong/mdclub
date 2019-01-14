<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use App\Helper\ArrayHelper;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 问答
 *
 * Class QuestionController
 * @package App\Controller
 */
class Question extends ControllerAbstracts
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
        return $this->container->view->render($response, '/question/index.php');
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
        return $this->container->view->render($response, '/question/info.php');
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
        $list = $this->container->questionService->getList(['user_id' => $user_id], true);

        return $this->success($response, $list);
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
        $userId = $this->container->roleService->userIdOrFail();
        $list = $this->container->questionService->getList(['user_id' => $userId], true);

        return $this->success($response, $list);
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
        $list = $this->container->questionService->getList(['topic_id' => $topic_id], true);

        return $this->success($response, $list);
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
        $list = $this->container->questionService->getList([], true);

        return $this->success($response, $list);
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
        $userId = $this->container->roleService->userIdOrFail();

        $questionId = $this->container->questionService->create(
            $userId,
            $request->getParsedBodyParam('title'),
            $request->getParsedBodyParam('content_markdown'),
            $request->getParsedBodyParam('content_rendered'),
            ArrayHelper::getParsedBodyParam($request, 'topic_id', 10)
        );

        $questionInfo = $this->container->questionService->get($questionId, true);

        return $this->success($response, $questionInfo);
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
        $this->container->roleService->managerIdOrFail();

        $questionIds = ArrayHelper::getQueryParam($request, 'question_id', 100);
        $this->container->questionService->deleteMultiple($questionIds);

        return $this->success($response);
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
        $questionInfo = $this->container->questionService->getOrFail($question_id, true);

        return $this->success($response, $questionInfo);
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
        $topicIds = ArrayHelper::getParsedBodyParam($request, 'topic_id', 10);

        $this->container->questionService->update($question_id, $title, $contentMarkdown, $contentRendered, $topicIds);
        $questionInfo = $this->container->questionService->get($question_id, true);

        return $this->success($response, $questionInfo);
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
        $this->container->questionService->delete($question_id);

        return $this->success($response);
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
        $list = $this->container->questionService->getComments($question_id, true);

        return $this->success($response, $list);
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
        $commentId = $this->container->questionService->addComment($question_id, $content);
        $comment = $this->container->commentService->get($commentId, true);

        return $this->success($response, $comment);
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
        $voters = $this->container->questionService->getVoters($question_id, $type, true);

        return $this->success($response, $voters);
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
        $userId = $this->container->roleService->userIdOrFail();
        $type = $request->getParsedBodyParam('type');

        $this->container->questionService->addVote($userId, $question_id, $type);
        $voteCount = $this->container->questionService->getVoteCount($question_id);

        return $this->success($response, ['vote_count' => $voteCount]);
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
        $userId = $this->container->roleService->userIdOrFail();

        $this->container->questionService->deleteVote($userId, $question_id);
        $voteCount = $this->container->questionService->getVoteCount($question_id);

        return $this->success($response, ['vote_count' => $voteCount]);
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
        $following = $this->container->questionService->getFollowing($user_id, true);

        return $this->success($response, $following);
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
        $userId = $this->container->roleService->userIdOrFail();
        $following = $this->container->questionService->getFollowing($userId, true);

        return $this->success($response, $following);
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
        $followers = $this->container->questionService->getFollowers($question_id, true);

        return $this->success($response, $followers);
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
        $userId = $this->container->roleService->userIdOrFail();

        $this->container->questionService->addFollow($userId, $question_id);
        $followerCount = $this->container->questionService->getFollowerCount($question_id);

        return $this->success($response, ['follower_count' => $followerCount]);
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
        $userId = $this->container->roleService->userIdOrFail();

        $this->container->questionService->deleteFollow($userId, $question_id);
        $followerCount = $this->container->questionService->getFollowerCount($question_id);

        return $this->success($response, ['follower_count' => $followerCount]);
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
        $this->container->roleService->managerIdOrFail();

        $list = $this->container->questionService->getList(['is_deleted' => true], true);

        return $this->success($response, $list);
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
        return $response;
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
        return $response;
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
        return $response;
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
        return $response;
    }
}
