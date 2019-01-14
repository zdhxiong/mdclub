<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use App\Helper\ArrayHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 用户
 *
 * Class UserController
 * @package App\Controller
 */
class User extends ControllerAbstracts
{
    /**
     * 用户列表页
     *
     * @param  Request           $request
     * @param  Response          $response
     * @return ResponseInterface
     */
    public function pageIndex(Request $request, Response $response): ResponseInterface
    {
        return $this->container->view->render($response, '/user/index.php');
    }

    /**
     * 用户详情页
     *
     * @param  Request           $request
     * @param  Response          $response
     * @param  int               $user_id
     * @return ResponseInterface
     */
    public function pageInfo(Request $request, Response $response, int $user_id): ResponseInterface
    {
        return $this->container->view->render($response, '/user/info.php');
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
        $list = $this->container->userService->getList([], true);

        return $this->success($response, $list);
    }

    /**
     * 批量禁用用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function disableMultiple(Request $request, Response $response): Response
    {
        $this->container->roleService->managerIdOrFail();

        $userIds = ArrayHelper::getQueryParam($request, 'user_id', 100);
        $this->container->userService->disableMultiple($userIds);

        return $this->success($response);
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
        $userInfo = $this->container->userService->getOrFail($user_id, true);

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
    public function updateOne(Request $request, Response $response, int $user_id): Response
    {
        $this->container->roleService->managerIdOrFail();

        $this->container->userService->update($user_id, $request->getParsedBody());
        $userInfo = $this->container->userService->get($user_id, true);

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
        $this->container->roleService->managerIdOrFail();

        $this->container->userService->disable($user_id);

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
        $userId = $this->container->roleService->userIdOrFail();

        $userInfo = $this->container->userService->get($userId, true);

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
        $userId = $this->container->roleService->userIdOrFail();

        $this->container->userService->update($userId, $request->getParsedBody());
        $userInfo = $this->container->userService->get($userId, true);

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
        $this->container->roleService->managerIdOrFail();

        $filename = $this->container->userAvatarService->delete($user_id);
        $newAvatars = $this->container->userAvatarService->getBrandUrls($user_id, $filename);

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
        $userId = $this->container->roleService->userIdOrFail();

        /** @var UploadedFileInterface $avatar */
        $avatar = $request->getUploadedFiles()['avatar'] ?? null;

        $filename = $this->container->userAvatarService->upload($userId, $avatar);
        $newAvatars = $this->container->userAvatarService->getBrandUrls($userId, $filename);

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
        $userId = $this->container->roleService->userIdOrFail();

        $filename = $this->container->userAvatarService->delete($userId);
        $newAvatars = $this->container->userAvatarService->getBrandUrls($userId, $filename);

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
        $this->container->roleService->managerIdOrFail();

        $this->container->userCoverService->delete($user_id);
        $newCovers = $this->container->userCoverService->getBrandUrls($user_id);

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
        $userId = $this->container->roleService->userIdOrFail();

        /** @var UploadedFileInterface $cover */
        $cover = $request->getUploadedFiles()['cover'] ?? null;

        $filename = $this->container->userCoverService->upload($userId, $cover);
        $newCovers = $this->container->userCoverService->getBrandUrls($userId, $filename);

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
        $userId = $this->container->roleService->userIdOrFail();

        $this->container->userCoverService->delete($userId);
        $newCovers = $this->container->userCoverService->getBrandUrls($userId);

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
        $followers = $this->container->userService->getFollowers($user_id, true);

        return $this->success($response, $followers);
    }

    /**
     * 添加关注
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function addFollow(Request $request, Response $response, int $user_id): Response
    {
        $currentUserId = $this->container->roleService->userIdOrFail();

        $this->container->userService->addFollow($currentUserId, $user_id);
        $followerCount = $this->container->userService->getFollowerCount($user_id);

        return $this->success($response, ['follower_count' => $followerCount]);
    }

    /**
     * 取消关注
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function deleteFollow(Request $request, Response $response, int $user_id): Response
    {
        $currentUserId = $this->container->roleService->userIdOrFail();

        $this->container->userService->deleteFollow($currentUserId, $user_id);
        $followerCount = $this->container->userService->getFollowerCount($user_id);

        return $this->success($response, ['follower_count' => $followerCount]);
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
        $following = $this->container->userService->getFollowing($user_id, true);

        return $this->success($response, $following);
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
        $userId = $this->container->roleService->userIdOrFail();

        $followers = $this->container->userService->getFollowers($userId, true);

        return $this->success($response, $followers);
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
        $userId = $this->container->roleService->userIdOrFail();

        $following = $this->container->userService->getFollowing($userId, true);

        return $this->success($response, $following);
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
        $this->container->userPasswordResetService->sendEmail($request->getParsedBodyParam('email'));

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
        $this->container->userPasswordResetService->doReset(
            $request->getParsedBodyParam('email'),
            $request->getParsedBodyParam('email_code'),
            $request->getParsedBodyParam('password')
        );

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
        $userInfo = $this->container->userRegisterService->doRegister(
            $request->getParsedBodyParam('email'),
            $request->getParsedBodyParam('email_code'),
            $request->getParsedBodyParam('username'),
            $request->getParsedBodyParam('password'),
            $request->getParsedBodyParam('device')
        );

        return $this->success($response, $userInfo);
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
        $this->container->userRegisterService->sendEmail($request->getParsedBodyParam('email'));

        return $this->success($response);
    }

    /**
     * 获取已禁用用户列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getDisabledList(Request $request, Response $response): Response
    {
        $this->container->roleService->managerIdOrFail();

        $list = $this->container->userService->getList(['is_disabled' => true], true);

        return $this->success($response, $list);
    }

    /**
     * 批量恢复用户
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function enableMultiple(Request $request, Response $response): Response
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
    public function enableOne(Request $request, Response $response, int $user_id): Response
    {
        return $response;
    }
}
