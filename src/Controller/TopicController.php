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
     * 获取指定话题信息
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function get(Request $request, Response $response, int $topic_id): Response
    {
        $topicInfo = $this->topicService->get($topic_id, true);

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
        $this->topicService->delete($topic_id);

        return $this->success($response);
    }

    /**
     * 批量删除话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function batchDelete(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $topicIds = $request->getQueryParam('topic_id');

        if ($topicIds) {
            $topicIds = array_unique(array_filter(array_slice(explode(',', $topicIds), 0, 100)));
        }

        if ($topicIds) {
            $this->topicService->batchDelete($topicIds);
        }

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
        $followerCount = $this->topicFollowService->getFollowerCount($topic_id);

        return $this->success($response, ['follower_count' => $followerCount]);
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
        $followerCount = $this->topicFollowService->getFollowerCount($topic_id);

        return $this->success($response, ['follower_count' => $followerCount]);
    }
}
