<?php

declare(strict_types=1);

namespace App\Controller;

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
        return $response;
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

        $name = $request->getParsedBodyParam('name');
        $description = $request->getParsedBodyParam('description');
        $cover = $request->getUploadedFiles()['cover'];



        return $response;
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

        return $response;
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
     * 获取指定话题的关注者
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $topic_id
     * @return Response
     */
    public function getFollowers(Request $request, Response $response, int $topic_id): Response
    {
        return $response;
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
        return $response;
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
        return $response;
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
        return $response;
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
        return $response;
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
        return $response;
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
        return $response;
    }
}
