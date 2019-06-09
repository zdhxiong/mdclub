<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ContainerAbstracts;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 用户
 */
class User extends ContainerAbstracts
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
        return $this->view->render($response, '/user/index.php');
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
        return $this->view->render($response, '/user/info.php');
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
        return $this->userService
            ->fetchCollection()
            ->getList([], true)
            ->render($response);
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
        $this->roleService->managerIdOrFail();

        $userIds = $this->requestService->getQueryParamToArray('user_id', 100);
        $this->userService->disableMultiple($userIds);

        return collect()->render($response);
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
        return $this->userService
            ->fetchCollection()
            ->getOrFail($user_id, true)
            ->render($response);
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
        $this->roleService->managerIdOrFail();

        $this->userService->update($user_id, $request->getParsedBody());

        return $this->userService
            ->fetchCollection()
            ->get($user_id, true)
            ->render($response);
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

        return collect()->render($response);
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

        return $this->userService
            ->fetchCollection()
            ->get($userId, true)
            ->render($response);
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

        return $this->userService
            ->fetchCollection()
            ->get($userId, true)
            ->render($response);
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

        return $this->userAvatarService
            ->fetchCollection()
            ->getBrandUrls($user_id, $filename)
            ->render($response);
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

        return $this->userAvatarService
            ->fetchCollection()
            ->getBrandUrls($userId, $filename)
            ->render($response);
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

        return $this->userAvatarService
            ->fetchCollection()
            ->getBrandUrls($userId, $filename)
            ->render($response);
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

        return $this->userCoverService
            ->fetchCollection()
            ->getBrandUrls($user_id)
            ->render($response);
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

        return $this->userCoverService
            ->fetchCollection()
            ->getBrandUrls($userId, $filename)
            ->render($response);
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

        return $this->userCoverService
            ->fetchCollection()
            ->getBrandUrls($userId)
            ->render($response);
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
        return $this->userService
            ->fetchCollection()
            ->getFollowers($user_id, true)
            ->render($response);
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
        $currentUserId = $this->roleService->userIdOrFail();

        $this->userService->addFollow($currentUserId, $user_id);
        $followerCount = $this->userService->getFollowerCount($user_id);

        return collect(['follower_count' => $followerCount])->render($response);
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
        $currentUserId = $this->roleService->userIdOrFail();

        $this->userService->deleteFollow($currentUserId, $user_id);
        $followerCount = $this->userService->getFollowerCount($user_id);

        return collect(['follower_count' => $followerCount])->render($response);
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
        return $this->userService
            ->fetchCollection()
            ->getFollowing($user_id, true)
            ->render($response);
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

        return $this->userService
            ->fetchCollection()
            ->getFollowers($userId, true)
            ->render($response);
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

        return $this->userService
            ->fetchCollection()
            ->getFollowing($userId, true)
            ->render($response);
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

        return collect()->render($response);
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

        return collect()->render($response);
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
        return $this->userRegisterService
            ->fetchCollection()
            ->doRegister(
                $request->getParsedBodyParam('email'),
                $request->getParsedBodyParam('email_code'),
                $request->getParsedBodyParam('username'),
                $request->getParsedBodyParam('password'),
                $request->getParsedBodyParam('device')
            )->render($response);
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

        return collect()->render($response);
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
        $this->roleService->managerIdOrFail();

        return $this->userService
            ->fetchCollection()
            ->getList(['is_disabled' => true], true)
            ->render($response);
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
        return collect()->render($response);
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
        return collect()->render($response);
    }
}
