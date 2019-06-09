<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 回答
 */
class Answer extends ContainerAbstracts
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
        return $this->answerGetService
            ->forApi()
            ->getByUserId($user_id)
            ->render($response);
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
        $userId = $this->roleService->userIdOrFail();

        return $this->answerGetService
            ->forApi()
            ->getByUserId($userId)
            ->render($response);
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
        return $this->answerGetService
            ->forApi()
            ->getByQuestionId($question_id)
            ->render($response);
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
        $answerId = $this->answerUpdateService->create(
            $question_id,
            $request->getParsedBodyParam('content_markdown'),
            $request->getParsedBodyParam('content_rendered')
        );

        return $this->answerGetService
            ->forApi()
            ->get($answerId)
            ->render($response);
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
        return $this->answerGetService
            ->forApi()
            ->getList()
            ->render($response);
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
        $this->roleService->managerIdOrFail();

        $answerIds = $this->requestService->getQueryParamToArray('answer_id', 100);
        $this->answerDeleteService->deleteMultiple($answerIds);

        return collect()->render($response);
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
        return $this->answerGetService
            ->forApi()
            ->getOrFail($answer_id)
            ->render($response);
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
        $this->answerUpdateService->update(
            $answer_id,
            $request->getParsedBodyParam('content_markdown'),
            $request->getParsedBodyParam('content_rendered')
        );

        return $this->answerGetService
            ->forApi()
            ->get($answer_id)
            ->render($response);
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
        $this->answerDeleteService->delete($answer_id);

        return collect()->render($response);
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
        return $this->commentGetService
            ->forApi()
            ->getByAnswerId($answer_id)
            ->render($response);
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
        $commentId = $this->commentUpdateService->createInAnswer($answer_id, $content);

        return $this->commentGetService
            ->forApi()
            ->get($commentId)
            ->render($response);
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

        return $this->answerVoteService
            ->forApi()
            ->getVoters($answer_id, $type)
            ->render($response);
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
        $userId = $this->roleService->userIdOrFail();
        $type = $request->getParsedBodyParam('type');

        $this->answerVoteService->add($userId, $answer_id, $type);
        $voteCount = $this->answerVoteService->getCount($answer_id);

        return collect(['vote_count' => $voteCount])->render($response);
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
        $userId = $this->roleService->userIdOrFail();

        $this->answerVoteService->delete($userId, $answer_id);
        $voteCount = $this->answerVoteService->getCount($answer_id);

        return collect(['vote_count' => $voteCount])->render($response);
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
        $this->roleService->managerIdOrFail();

        return $this->answerGetService
            ->forApi()
            ->getDeleted()
            ->render($response);
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
        $this->roleService->managerIdOrFail();

        $answerIds = $this->requestService->getQueryParamToArray('answer_id', 100);
        $this->answerDeleteService->restoreMultiple($answerIds);

        return collect()->render($response);
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
        $this->roleService->managerIdOrFail();

        $answerIds = $this->requestService->getQueryParamToArray('answer_id', 100);
        $this->answerDeleteService->destroyMultiple($answerIds);

        return collect()->render($response);
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
        $this->roleService->managerIdOrFail();
        $this->answerDeleteService->restore($answer_id);

        return collect()->render($response);
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
        $this->roleService->managerIdOrFail();
        $this->answerDeleteService->destroy($answer_id);

        return collect()->render($response);
    }
}
