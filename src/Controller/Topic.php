<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 话题
 */
class Topic extends ContainerAbstracts
{
    /**
     * 话题列表页
     *
     * @param  Request           $request
     * @param  Response          $response
     * @return ResponseInterface
     */
    public function pageIndex(Request $request, Response $response): ResponseInterface
    {
        return $this->view->render($response, '/topic/index.php');
    }

    /**
     * 话题详情页
     *
     * @param  Request           $request
     * @param  Response          $response
     * @param  int               $topic_id
     * @return ResponseInterface
     */
    public function pageInfo(Request $request, Response $response, int $topic_id): ResponseInterface
    {
        return $this->view->render($response, '/topic/info.php');
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
        return $this->topicGetService
            ->forApi()
            ->getList()
            ->render($response);
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

        $topicId = $this->topicUpdateService->create($name, $description, $cover);

        return $this->topicGetService
            ->forApi()
            ->get($topicId)
            ->render($response);
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

        $topicIds = $this->requestService->getQueryParamToArray('topic_id', 100);
        $this->topicService->deleteMultiple($topicIds);

        return collect()->render($response);
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
        return $this->topicGetService
            ->forApi()
            ->getOrFail($topic_id)
            ->render($response);
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

        $this->topicUpdateService->update($topic_id, $name, $description, $cover);

        return $this->topicGetService
            ->forApi()
            ->get($topic_id)
            ->render($response);
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

        return collect()->render($response);
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
        return $this->topicService
            ->fetchCollection()
            ->getFollowing($user_id, true)
            ->render($response);
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

        return $this->topicService
            ->fetchCollection()
            ->getFollowing($userId, true)
            ->render($response);
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
        return $this->topicService
            ->fetchCollection()
            ->getFollowers($topic_id, true)
            ->render($response);
    }

    /**
     * 添加关注
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

        return collect(['follower_count' => $followerCount])->render($response);
    }

    /**
     * 取消关注
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

        return collect(['follower_count' => $followerCount])->render($response);
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

        return $this->topicGetService
            ->forApi()
            ->getDeleted()
            ->render($response);
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
        return collect()->render($response);
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
        return collect()->render($response);
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
        return collect()->render($response);
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
        return collect()->render($response);
    }
}
