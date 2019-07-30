<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\Abstracts;
use MDClub\Helper\Request;
use MDClub\Middleware\Transform\Answer;
use Psr\Http\Message\UploadedFileInterface;

/**
 * 用户 restful api
 */
class User extends Abstracts
{
    /**
     * 获取用户列表
     *
     * @uses \MDClub\Middleware\Transform\User
     * @return array
     */
    public function getList(): array
    {
        return $this->userGetService->getList();
    }

    /**
     * 注册账号
     *
     * @return array
     */
    public function register(): array
    {
        return $this->userRegisterService
            ->register(
                $this->request->getParsedBody()['email'] ?? '',
                $this->request->getParsedBody()['email_code'] ?? '',
                $this->request->getParsedBody()['username'] ?? '',
                $this->request->getParsedBody()['password'] ?? '',
                $this->request->getParsedBody()['device'] ?? ''
            );
    }

    /**
     * 批量禁用用户
     *
     * @uses NeedManager
     * @return array
     */
    public function disableMultiple(): array
    {
        $userIds = Request::getQueryParamToArray($this->request, 'user_id', 100) ?? [];

        $this->userDisableService->disableMultiple($userIds);

        return [];
    }

    /**
     * 获取指定用户的信息
     *
     * @uses   \MDClub\Middleware\Transform\User
     * @param  int      $user_id
     * @return array
     */
    public function get(int $user_id): array
    {
        return $this->userGetService->getOrFail($user_id);
    }

    /**
     * 更新指定用户信息
     *
     * @uses NeedManager
     * @param  int      $user_id
     * @return array
     */
    public function update(int $user_id): array
    {
        $this->userUpdateService->update($user_id, $this->request->getParsedBody());

        return $this->userGetService->get($user_id);
    }

    /**
     * 禁用指定用户，实质上是软删除用户，用户不能物理删除
     *
     * @uses NeedManager
     * @param  int      $user_id
     * @return array
     */
    public function disable(int $user_id): array
    {
        $this->userDisableService->disable($user_id);

        return [];
    }

    /**
     * 删除指定用户的头像
     *
     * @uses NeedManager
     * @param  int      $user_id
     * @return array
     */
    public function deleteAvatar(int $user_id): array
    {
        $filename = $this->userAvatarService->delete($user_id);

        return $this->userAvatarService->getBrandUrls($user_id, $filename);
    }

    /**
     * 删除指定用户的封面
     *
     * @uses NeedManager
     * @param  int      $user_id
     * @return array
     */
    public function deleteCover(int $user_id): array
    {
        $this->userCoverService->delete($user_id);

        return $this->userCoverService->getBrandUrls($user_id);
    }

    /**
     * 获取指定用户的关注者
     *
     * @uses   \MDClub\Middleware\Transform\User
     * @param  int      $user_id
     * @return array
     */
    public function getFollowers(int $user_id): array
    {
        return $this->userFollowService->getFollowers($user_id);
    }

    /**
     * 添加关注
     *
     * @uses NeedLogin
     * @param  int      $user_id
     * @return array
     */
    public function addFollow(int $user_id): array
    {
        $this->userFollowService->addFollow($user_id);
        $followerCount = $this->userFollowService->getFollowerCount($user_id);

        return ['follower_count' => $followerCount];
    }

    /**
     * 取消关注
     *
     * @uses NeedLogin
     * @param  int      $user_id
     * @return array
     */
    public function deleteFollow(int $user_id): array
    {
        $this->userFollowService->deleteFollow($user_id);
        $followerCount = $this->userFollowService->getFollowerCount($user_id);

        return ['follower_count' => $followerCount];
    }

    /**
     * 获取指定用户关注的人
     *
     * @uses   \MDClub\Middleware\Transform\User
     * @param  int $user_id
     * @return array
     */
    public function getFollowees(int $user_id): array
    {
        return $this->userFollowService->getFollowing($user_id);
    }

    /**
     * 获取指定用户关注的提问
     *
     * @param  int      $user_id
     * @return array
     */
    public function getFollowingQuestions(int $user_id): array
    {
        return $this->questionFollowService->getFollowing($user_id);
    }

    /**
     * 获取指定用户关注的文章列表
     *
     * @param  int   $user_id
     * @return array
     */
    public function getFollowingArticles(int $user_id): array
    {
        return $this->articleFollowService->getFollowing($user_id);
    }

    /**
     * 获取指定用户关注的话题列表
     *
     * @param  int      $user_id
     * @return array
     */
    public function getFollowingTopics(int $user_id): array
    {
        return $this->topicFollowService->getFollowing($user_id);
    }

    /**
     * 获取指定用户发表的提问列表
     *
     * @param  int      $user_id
     * @return array
     */
    public function getQuestions(int $user_id): array
    {
        return $this->questionGetService->getByUserId($user_id);
    }

    /**
     * 获取指定用户发表的回答列表
     *
     * @uses   Answer
     * @param  int    $user_id
     * @return array
     */
    public function getAnswers(int $user_id): array
    {
        return $this->answerGetService->getByUserId($user_id);
    }

    /**
     * 获取指定用户发表的文章列表
     *
     * @param  int   $user_id
     * @return array
     */
    public function getArticles(int $user_id): array
    {
        return $this->articleGetService->getByUserId($user_id);
    }

    /**
     * 获取指定用户发表的评论列表
     *
     * @param  int      $user_id
     * @return array
     */
    public function getComments(int $user_id): array
    {
        return $this->commentGetService->getByUserId($user_id);
    }

    /**
     * 获取我的用户信息
     *
     * @return array
     *@uses User
     * @uses NeedLogin
     */
    public function getMine(): array
    {
        $userId = $this->auth->userId();

        return $this->userGetService->get($userId);
    }

    /**
     * 更新我的用户信息
     *
     * @return array
     */
    public function updateMine(): array
    {
        $userId = $this->auth->userId();
        $this->userUpdateService->update($userId, $this->request->getParsedBody());

        return $this->userGetService->get($userId);
    }

    /**
     * 上传我的头像
     *
     * @uses NeedLogin
     * @return array
     */
    public function uploadMyAvatar(): array
    {
        /** @var UploadedFileInterface $avatar */
        $avatar = $this->request->getUploadedFiles()['avatar'] ?? null;

        $userId = $this->auth->userId();
        $filename = $this->userAvatarService->upload($userId, $avatar);

        return $this->userAvatarService->getBrandUrls($userId, $filename);
    }

    /**
     * 删除我的的头像
     *
     * @uses NeedLogin
     * @return array
     */
    public function deleteMyAvatar(): array
    {
        $userId = $this->auth->userId();
        $filename = $this->userAvatarService->delete($userId);

        return $this->userAvatarService->getBrandUrls($userId, $filename);
    }

    /**
     * 上传我的封面
     *
     * @uses NeedLogin
     * @return array
     */
    public function uploadMyCover(): array
    {
        /** @var UploadedFileInterface $cover */
        $cover = $this->request->getUploadedFiles()['cover'] ?? null;

        $userId = $this->auth->userId();
        $filename = $this->userCoverService->upload($userId, $cover);

        return $this->userCoverService->getBrandUrls($userId, $filename);
    }

    /**
     * 删除我的封面
     *
     * @uses NeedLogin
     * @return array
     */
    public function deleteMyCover(): array
    {
        $userId = $this->auth->userId();
        $this->userCoverService->delete($userId);

        return $this->userCoverService->getBrandUrls($userId);
    }

    /**
     * 发送注册验证邮件
     *
     * @return array
     */
    public function sendRegisterEmail(): array
    {
        $this->userRegisterService->sendEmail(
            $this->request->getParsedBody()['email'] ?? ''
        );

        return [];
    }

    /**
     * 发送重置密码验证邮件
     *
     * @return array
     */
    public function sendPasswordResetEmail(): array
    {
        $this->userPasswordResetService->sendEmail(
            $this->request->getParsedBody()['email'] ?? ''
        );

        return [];
    }

    /**
     * 重置密码
     *
     * @return array
     */
    public function updatePassword(): array
    {
        $this->userPasswordResetService->reset(
            $this->request->getParsedBody()['email'] ?? '',
            $this->request->getParsedBody()['email_code'] ?? '',
            $this->request->getParsedBody()['password'] ?? ''
        );

        return [];
    }

    /**
     * 获取我的关注者
     *
     * @return array
     *@uses User
     * @uses NeedLogin
     */
    public function getMyFollowers(): array
    {
        $userId = $this->auth->userId();

        return $this->userFollowService->getFollowers($userId);
    }

    /**
     * 获取我关注的人
     *
     * @return array
     *@uses User
     * @uses NeedLogin
     */
    public function getMyFollowees(): array
    {
        $userId = $this->auth->userId();

        return $this->userFollowService->getFollowing($userId);
    }

    /**
     * 获取我关注的提问
     *
     * @return array
     */
    public function getMyFollowingQuestions(): array
    {
        return $this->questionFollowService->getFollowing($this->auth->userId());
    }

    /**
     * 获取当前用户关注的文章列表
     *
     * @return array
     */
    public function getMyFollowingArticles(): array
    {
        return $this->articleFollowService->getFollowing($this->auth->userId());
    }

    /**
     * 获取我关注的话题
     *
     * @return array
     */
    public function getMyFollowingTopics(): array
    {
        return $this->topicFollowService->getFollowing($this->auth->userId());
    }

    /**
     * 获取当前用户发表的提问列表
     *
     * @return array
     */
    public function getMyQuestions(): array
    {
        return $this->questionGetService->getByUserId($this->auth->userId());
    }

    /**
     * 获取当前用户发表的回答列表
     *
     * @uses NeedLogin
     * @return array
     */
    public function getMyAnswers(): array
    {
        return $this->answerGetService->getByUserId($this->auth->userId());
    }

    /**
     * 获取当前用户发表的文章列表
     *
     * @return array
     */
    public function getMyArticles(): array
    {
        return $this->articleGetService->getByUserId($this->auth->userId());
    }

    /**
     * 获取当前用户发表的评论列表
     *
     * @return array
     */
    public function getMyComments(): array
    {
        return $this->commentGetService->getByUserId($this->auth->userId());
    }

    /**
     * 获取已禁用用户列表
     *
     * @return array
     *@uses User
     * @uses NeedManager
     */
    public function getDisabled(): array
    {
        return $this->userGetService->getDisabled();
    }

    /**
     * 批量恢复用户
     *
     * @uses NeedManager
     * @return array
     */
    public function enableMultiple(): array
    {
        $userIds = Request::getQueryParamToArray($this->request, 'user_id', 100) ?? [];

        $this->userDisableService->enableMultiple($userIds);

        return [];
    }

    /**
     * 恢复指定用户
     *
     * @uses NeedManager
     * @param  int      $user_id
     * @return array
     */
    public function enable(int $user_id): array
    {
        $this->userDisableService->enable($user_id);

        return [];
    }
}
