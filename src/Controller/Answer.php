<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use App\Helper\ArrayHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 回答
 *
 * Class Answer
 * @package App\Controller
 */
class Answer extends ControllerAbstracts
{
    /**
     * 获取指定用户发表的回答列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getListByUserId(Request $request, Response $response, int $user_id): Response
    {
        $list = $this->container->answerService->getList(['user_id' => $user_id], true);

        return $this->success($response, $list);
    }

    /**
     * 获取当前用户发表的回答列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMyList(Request $request, Response $response): Response
    {
        $userId = $this->container->roleService->userIdOrFail();
        $list = $this->container->answerService->getList(['user_id' => $userId], true);

        return $this->success($response, $list);
    }

    /**
     * 获取指定提问下的回答列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function getListByQuestionId(Request $request, Response $response, int $question_id): Response
    {
        $list = $this->container->answerService->getList(['question_id' => $question_id], true);

        return $this->success($response, $list);
    }

    /**
     * 在指定提问下创建回答
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $question_id
     * @return Response
     */
    public function create(Request $request, Response $response, int $question_id): Response
    {
        $this->container->roleService->userIdOrFail();

        $answerId = $this->container->answerService->create(
            $question_id,
            $request->getParsedBodyParam('content_markdown'),
            $request->getParsedBodyParam('content_rendered')
        );

        $answerInfo = $this->container->answerService->get($answerId, true);

        return $this->success($response, $answerInfo);
    }

    /**
     * 获取回答列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        $list = $this->container->answerService->getList([], true);

        return $this->success($response, $list);
    }

    /**
     * 批量删除回答
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function deleteMultiple(Request $request, Response $response): Response
    {
        $this->container->roleService->managerIdOrFail();

        $answerIds = ArrayHelper::getQueryParam($request, 'answer_id', 100);
        $this->container->answerService->deleteMultiple($answerIds);

        return $this->success($response);
    }


    /**
     * 获取指定回答的详情
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function getOne(Request $request, Response $response, int $answer_id): Response
    {
        $answerInfo = $this->container->answerService->getOrFail($answer_id, true);

        return $this->success($response, $answerInfo);
    }

    /**
     * 更新指定回答
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function updateOne(Request $request, Response $response, int $answer_id): Response
    {
        $this->container->answerService->update(
            $answer_id,
            $request->getParsedBodyParam('content_markdown'),
            $request->getParsedBodyParam('content_rendered')
        );

        $answerInfo = $this->container->answerService->get($answer_id, true);

        return $this->success($response, $answerInfo);
    }

    /**
     * 删除指定回答
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function deleteOne(Request $request, Response $response, int $answer_id): Response
    {
        $this->container->answerService->delete($answer_id);

        return $this->success($response);
    }

    /**
     * 获取指定回答下的评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function getComments(Request $request, Response $response, int $answer_id): Response
    {
        $list = $this->container->answerService->getComments($answer_id, true);

        return $this->success($response, $list);
    }

    /**
     * 在指定回答下发表评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function addComment(Request $request, Response $response, int $answer_id): Response
    {
        $content = $request->getParsedBodyParam('content');
        $commentId = $this->container->answerService->addComment($answer_id, $content);
        $comment = $this->container->commentService->get($commentId, true);

        return $this->success($response, $comment);
    }

    /**
     * 获取投票者
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function getVoters(Request $request, Response $response, int $answer_id): Response
    {
        $type = $request->getQueryParam('type');
        $voters = $this->container->answerService->getVoters($answer_id, $type, true);

        return $this->success($response, $voters);
    }

    /**
     * 添加投票
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function addVote(Request $request, Response $response, int $answer_id): Response
    {
        $userId = $this->container->roleService->userIdOrFail();
        $type = $request->getParsedBodyParam('type');

        $this->container->answerService->addVote($userId, $answer_id, $type);
        $voteCount = $this->container->answerService->getVoteCount($answer_id);

        return $this->success($response, ['vote_count' => $voteCount]);
    }

    /**
     * 删除投票
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function deleteVote(Request $request, Response $response, int $answer_id): Response
    {
        $userId = $this->container->roleService->userIdOrFail();

        $this->container->answerService->deleteVote($userId, $answer_id);
        $voteCount = $this->container->answerService->getVoteCount($answer_id);

        return $this->success($response, ['vote_count' => $voteCount]);
    }

    /**
     * 获取回收站中的回答列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getDeletedList(Request $request, Response $response): Response
    {
        $this->container->roleService->managerIdOrFail();

        $list = $this->container->answerService->getList(['is_deleted' => true], true);

        return $this->success($response, $list);
    }

    /**
     * 批量恢复回答
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
     * 批量删除回收站中的回答
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
     * 恢复指定回答
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function restoreOne(Request $request, Response $response, int $answer_id): Response
    {
        return $response;
    }

    /**
     * 删除指定回答
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $answer_id
     * @return Response
     */
    public function destroyOne(Request $request, Response $response, int $answer_id): Response
    {
        return $response;
    }
}
