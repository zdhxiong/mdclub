<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 用户
 *
 * Class UserController
 * @package App\Controller
 */
class UserController extends ControllerAbstracts
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
     * 获取指定用户的信息
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function get(Request $request, Response $response, int $user_id): Response
    {
        $userInfo = $this->userService->getOrFail($user_id, true);

        return $this->success($response, $userInfo);
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
     * 更新指定用户信息
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function update(Request $request, Response $response, int $user_id): Response
    {
        $this->roleService->managerIdOrFail();

        $this->userService->update($user_id, $request->getParsedBody());
        $userInfo = $this->userService->get($user_id, true);

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
     * 禁用指定用户，实质上是软删除用户，用户不能物理删除
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function disable(Request $request, Response $response, int $user_id): Response
    {
        $this->roleService->managerIdOrFail();
        $this->userService->disable($user_id);

        return $this->success($response);
    }

    /**
     * 批量禁用用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function batchDisable(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $userIds = $request->getQueryParam('user_id');

        if ($userIds) {
            $userIds = array_unique(array_filter(array_slice(explode(',', $userIds), 0, 100)));
        }

        if ($userIds) {
            $this->userService->batchDisable($userIds);
        }

        return $this->success($response);
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
        $this->roleService->managerIdOrFail();

        $filename = $this->userAvatarService->delete($user_id);
        $newAvatars = $this->userAvatarService->getBrandUrls($user_id, $filename);

        return $this->success($response, $newAvatars);
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
        $userId = $this->roleService->userIdOrFail();

        $filename = $this->userAvatarService->delete($userId);
        $newAvatars = $this->userAvatarService->getBrandUrls($userId, $filename);

        return $this->success($response, $newAvatars);
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
        $userId = $this->roleService->userIdOrFail();

        /** @var UploadedFileInterface $avatar */
        $avatar = $request->getUploadedFiles()['avatar'] ?? null;

        $filename = $this->userAvatarService->upload($userId, $avatar);
        $newAvatars = $this->userAvatarService->getBrandUrls($userId, $filename);

        return $this->success($response, $newAvatars);
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
        $this->roleService->managerIdOrFail();

        $this->userCoverService->delete($user_id);
        $newCovers = $this->userCoverService->getBrandUrls($user_id);

        return $this->success($response, $newCovers);
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
        $userId = $this->roleService->userIdOrFail();

        $this->userCoverService->delete($userId);
        $newCovers = $this->userCoverService->getBrandUrls($userId);

        return $this->success($response, $newCovers);
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
        $userId = $this->roleService->userIdOrFail();

        /** @var UploadedFileInterface $cover */
        $cover = $request->getUploadedFiles()['cover'] ?? null;

        $filename = $this->userCoverService->upload($userId, $cover);
        $newCovers = $this->userCoverService->getBrandUrls($userId, $filename);

        return $this->success($response, $newCovers);
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
        $followers = $this->userService->getFollowers($user_id, true);

        return $this->success($response, $followers);
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
        $followers = $this->userService->getFollowers($userId, true);

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
    public function getFollowees(Request $request, Response $response, int $user_id): Response
    {
        $following = $this->userService->getFollowing($user_id, true);

        return $this->success($response, $following);
    }

    /**
     * 获取我关注的人
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMyFollowees(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $following = $this->userService->getFollowing($userId, true);

        return $this->success($response, $following);
    }

    /**
     * 关注某一用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function addFollow(Request $request, Response $response, int $user_id): Response
    {
        $currentUserId = $this->roleService->userIdOrFail();
        $this->userService->addFollow($currentUserId, $user_id);
        $followerCount = $this->userService->getFollowerCount($user_id);

        return $this->success($response, ['follower_count' => $followerCount]);
    }

    /**
     * 取消关注某一用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function deleteFollow(Request $request, Response $response, int $user_id): Response
    {
        $currentUserId = $this->roleService->userIdOrFail();
        $this->userService->deleteFollow($currentUserId, $user_id);
        $followerCount = $this->userService->getFollowerCount($user_id);

        return $this->success($response, ['follower_count' => $followerCount]);
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

    /**
     * 获取已禁用用户列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getDisabled(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 批量恢复用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function batchEnable(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 恢复指定用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function enable(Request $request, Response $response, int $user_id): Response
    {
        return $response;
    }
}
