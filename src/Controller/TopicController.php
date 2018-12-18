<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use App\Helper\ArrayHelper;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 话题
 *
 * Class TopicController
 * @package App\Controller
 */
class TopicController extends ControllerAbstracts
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
        $list = $this->topicService->getList([], true);

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
     * 批量删除话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function deleteMultiple(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $topicIds = ArrayHelper::parseQuery($request, 'topic_id', 100);
        $this->topicService->deleteMultiple($topicIds);

        return $this->success($response);
    }

    /**
     * 获取指定话题信息
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function getOne(Request $request, Response $response, int $topic_id): Response
    {
        $topicInfo = $this->topicService->getOrFail($topic_id, true);

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
    public function updateOne(Request $request, Response $response, int $topic_id): Response
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
    public function deleteOne(Request $request, Response $response, int $topic_id): Response
    {
        $this->roleService->managerIdOrFail();
        $this->topicService->delete($topic_id);

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
        $following= $this->topicService->getFollowing($user_id, true);

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
        $following = $this->topicService->getFollowing($userId, true);

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
        $followers = $this->topicService->getFollowers($topic_id, true);

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
        $this->topicService->addFollow($userId, $topic_id);
        $followerCount = $this->topicService->getFollowerCount($topic_id);

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
        $this->topicService->deleteFollow($userId, $topic_id);
        $followerCount = $this->topicService->getFollowerCount($topic_id);

        return $this->success($response, ['follower_count' => $followerCount]);
    }

    /**
     * 获取回收站中的话题列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getDeletedList(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $list = $this->topicService->getList(['is_deleted' => true], true);

        return $this->success($response, $list);
    }

    /**
     * 批量恢复话题
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
     * 批量从回收站中删除话题
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
     * 恢复指定话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function restoreOne(Request $request, Response $response, int $topic_id): Response
    {
        return $response;
    }

    /**
     * 从回收站中删除指定话题
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function destroyOne(Request $request, Response $response, int $topic_id): Response
    {
        return $response;
    }
}
