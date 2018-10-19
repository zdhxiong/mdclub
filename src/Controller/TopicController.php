<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 话题
 *
 * Class TopicController
 * @package App\Controller
 */
class TopicController extends Controller
{
    /**
     * 话题列表页
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function pageIndex(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 话题详情页
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function pageDetail(Request $request, Response $response, int $topic_id): Response
    {
        return $response;
    }

    /**
     * 获取话题列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        $list = $this->topicService->getList(true);

        return $this->success($response, $list);
    }

    /**
     * 创建话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        /** @var UploadedFileInterface $cover */
        $cover = $request->getUploadedFiles()['cover'] ?? null;
        $name = $request->getParsedBodyParam('name');
        $description = $request->getParsedBodyParam('description');

        $topicId = $this->topicService->create($name, $description, $cover);
        $topicInfo = $this->topicService->get($topicId, true);

        return $this->success($response, $topicInfo);
    }

    /**
     * 更新话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function update(Request $request, Response $response, int $topic_id): Response
    {
        $this->roleService->managerIdOrFail();

        /** @var UploadedFileInterface $cover */
        $cover = $request->getUploadedFiles()['cover'] ?? null;
        $name = $request->getParsedBodyParam('name');
        $description = $request->getParsedBodyParam('description');

        $this->topicService->update($topic_id, $name, $description, $cover);
        $topicInfo = $this->topicService->get($topic_id, true);

        return $this->success($response, $topicInfo);
    }

    /**
     * 删除话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function delete(Request $request, Response $response, int $topic_id): Response
    {
        $this->roleService->managerIdOrFail();

        $softDelete = !!$request->getQueryParam('soft_delete', 1);

        $this->topicService->delete($topic_id, $softDelete);

        return $this->success($response);
    }

    /**
     * 获取指定用户关注的话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getFollowing(Request $request, Response $response, int $user_id): Response
    {
        $following= $this->topicFollowService->getFollowing($user_id, true);

        return $this->success($response, $following);
    }

    /**
     * 获取指定用户是否已关注指定话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @param  int      $topic_id
     * @return Response
     */
    public function isFollowing(Request $request, Response $response, int $user_id, int $topic_id): Response
    {
        $isFollowing = $this->topicFollowService->isFollowing($user_id, $topic_id);

        return $this->success($response, $isFollowing);
    }

    /**
     * 获取我关注的话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMyFollowing(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $following = $this->topicFollowService->getFollowing($userId, true);

        return $this->success($response, $following);
    }

    /**
     * 检查我是否关注了指定话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function isMyFollowing(Request $request, Response $response, int $topic_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $isFollowing = $this->topicFollowService->isFollowing($userId, $topic_id);

        return $this->success($response, $isFollowing);
    }

    /**
     * 关注指定话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function addFollow(Request $request, Response $response, int $topic_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $this->topicFollowService->addFollow($userId, $topic_id);

        return $this->success($response);
    }

    /**
     * 取消关注指定话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function deleteFollow(Request $request, Response $response, int $topic_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $this->topicFollowService->deleteFollow($userId, $topic_id);

        return $this->success($response);
    }

    /**
     * 获取指定话题的关注者
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function getFollowers(Request $request, Response $response, int $topic_id): Response
    {
        $followers = $this->topicFollowService->getFollowers($topic_id, true);

        return $this->success($response, $followers);
    }
}
