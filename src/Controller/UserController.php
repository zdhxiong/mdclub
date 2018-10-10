<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 用户
 *
 * Class UserController
 * @package App\Controller
 */
class UserController extends Controller
{
    /**
     * 用户列表页
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
     * 用户详情页
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function pageDetail(Request $request, Response $response, int $user_id): Response
    {
        return $response;
    }

    /**
     * 获取用户列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        $list = $this->userService->getList(true);

        return $this->success($response, $list);
    }

    /**
     * 获取指定用户的信息
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getOne(Request $request, Response $response, int $user_id): Response
    {
        $userInfo = $this->userService->get($user_id, true);

        return $this->success($response, $userInfo);
    }

    /**
     * 禁用指定用户，实质上是软删除用户，用户不能物理删除
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function disableOne(Request $request, Response $response, int $user_id): Response
    {
        $this->roleService->managerIdOrFail();
        $this->userService->disable($user_id);

        return $this->success($response);
    }

    /**
     * 获取我的用户信息
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMe(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $userInfo = $this->userService->get($userId, true);

        return $this->success($response, $userInfo);
    }

    /**
     * 更新我的用户信息
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function updateMe(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $this->userService->update($userId, $request->getParsedBody());
        $userInfo = $this->userService->get($userId, true);

        return $this->success($response, $userInfo);
    }

    /**
     * 删除指定用户的头像
     *
     * @param Request $request
     * @param Response $response
     * @param int $user_id
     *
     * @return Response
     */
    public function deleteAvatar(Request $request, Response $response, int $user_id): Response
    {
        return $response;
    }

    /**
     * 上传我的头像
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function uploadMyAvatar(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 删除我的的头像
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function deleteMyAvatar(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 删除指定用户的封面
     *
     * @param Request $request
     * @param Response $response
     * @param int $user_id
     *
     * @return Response
     */
    public function deleteCover(Request $request, Response $response, int $user_id): Response
    {
        return $response;
    }

    /**
     * 上传我的封面
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function uploadMyCover(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 删除我的封面
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function deleteMyCover(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 获取指定用户的关注者
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getFollowers(Request $request, Response $response, int $user_id): Response
    {
        $followers = $this->userFollowService->getFollowers($user_id, true);

        return $this->success($response, $followers);
    }

    /**
     * 获取指定用户关注的人
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getFollowing(Request $request, Response $response, int $user_id): Response
    {
        $following = $this->userFollowService->getFollowing($user_id, true);

        return $this->success($response, $following);
    }

    /**
     * 获取指定用户是否已关注另一用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @param  int      $target_user_id
     * @return Response
     */
    public function isFollowing(Request $request, Response $response, int $user_id, int $target_user_id): Response
    {
        $isFollowing = $this->userFollowService->isFollowing($user_id, $target_user_id);

        return $this->success($response, $isFollowing);
    }

    /**
     * 获取我的关注者
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMyFollowers(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $followers = $this->userFollowService->getFollowers($userId, true);

        return $this->success($response, $followers);
    }

    /**
     * 获取我关注的人
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMyFollowing(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $following = $this->userFollowService->getFollowing($userId, true);

        return $this->success($response, $following);
    }

    /**
     * 关注某一用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $target_user_id
     * @return Response
     */
    public function addFollow(Request $request, Response $response, int $target_user_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $this->userFollowService->addFollow($userId, $target_user_id);

        return $this->success($response);
    }

    /**
     * 取消关注某一用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $target_user_id
     * @return Response
     */
    public function deleteFollow(Request $request, Response $response, int $target_user_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $this->userFollowService->deleteFollow($userId, $target_user_id);

        return $this->success($response);
    }

    /**
     * 获取我是否关注了某一用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $target_user_id
     * @return Response
     */
    public function isMyFollowing(Request $request, Response $response, int $target_user_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $isFollowing = $this->userFollowService->isFollowing($userId, $target_user_id);

        return $this->success($response, $isFollowing);
    }

    /**
     * 发送注册验证邮件
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function sendRegisterEmail(Request $request, Response $response): Response
    {
        $this->userRegisterService->sendEmail($request->getParsedBodyParam('email'));

        return $this->success($response);
    }

    /**
     * 注册账号
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $userInfo = $this->userRegisterService->doRegister(
            $request->getParsedBodyParam('email'),
            $request->getParsedBodyParam('email_code'),
            $request->getParsedBodyParam('username'),
            $request->getParsedBodyParam('password'),
            $request->getParsedBodyParam('device')
        );

        return $this->success($response, $userInfo);
    }

    /**
     * 发送重置密码验证邮件
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function sendResetEmail(Request $request, Response $response): Response
    {
        $this->userPasswordResetService->sendEmail($request->getParsedBodyParam('email'));

        return $this->success($response);
    }

    /**
     * 重置密码
     *
     * @param Request $request
     * @param Response $response
     *
     * @return Response
     */
    public function updatePasswordByEmail(Request $request, Response $response): Response
    {
        $this->userPasswordResetService->doReset(
            $request->getParsedBodyParam('email'),
            $request->getParsedBodyParam('email_code'),
            $request->getParsedBodyParam('password')
        );

        return $this->success($response);
    }
}
