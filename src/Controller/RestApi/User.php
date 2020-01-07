<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Controller\RestApi\Traits\Followable;
use MDClub\Controller\RestApi\Traits\Getable;
use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Service\AnswerService;
use MDClub\Facade\Service\ArticleService;
use MDClub\Facade\Service\CommentService;
use MDClub\Facade\Service\QuestionService;
use MDClub\Facade\Service\TopicService;
use MDClub\Facade\Service\UserAvatarService;
use MDClub\Facade\Service\UserCoverService;
use MDClub\Facade\Service\UserService;

/**
 * 用户 API
 */
class User extends Abstracts
{
    use Followable;
    use Getable;

    /**
     * @inheritDoc
     */
    protected function getService(): string
    {
        return \MDClub\Service\User::class;
    }

    /**
     * 注册账号
     *
     * @return array
     */
    public function register(): array
    {
        return UserService::register(Request::getParsedBody());
    }

    /**
     * 更新指定用户信息
     *
     * @param  int      $userId
     * @return array
     */
    public function update(int $userId): array
    {
        UserService::update($userId, Request::getParsedBody());

        return UserService::get($userId);
    }

    /**
     * 删除指定用户的头像
     *
     * @param  int      $userId
     * @return array
     */
    public function deleteAvatar(int $userId): array
    {
        $filename = UserAvatarService::delete($userId);

        return UserAvatarService::getBrandUrls($userId, $filename);
    }

    /**
     * 删除指定用户的封面
     *
     * @param  int      $userId
     * @return array
     */
    public function deleteCover(int $userId): array
    {
        UserCoverService::delete($userId);

        return UserCoverService::getBrandUrls($userId);
    }

    /**
     * 获取指定用户关注的人
     *
     * @param  int $userId
     * @return array
     */
    public function getFollowees(int $userId): array
    {
        return UserService::getFollowing($userId);
    }

    /**
     * 获取指定用户关注的提问
     *
     * @param  int      $userId
     * @return array
     */
    public function getFollowingQuestions(int $userId): array
    {
        return QuestionService::getFollowing($userId);
    }

    /**
     * 获取指定用户关注的文章列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getFollowingArticles(int $userId): array
    {
        return ArticleService::getFollowing($userId);
    }

    /**
     * 获取指定用户关注的话题列表
     *
     * @param  int      $userId
     * @return array
     */
    public function getFollowingTopics(int $userId): array
    {
        return TopicService::getFollowing($userId);
    }

    /**
     * 获取指定用户发表的提问列表
     *
     * @param  int      $userId
     * @return array
     */
    public function getQuestions(int $userId): array
    {
        return QuestionService::getByUserId($userId);
    }

    /**
     * 获取指定用户发表的回答列表
     *
     * @param  int    $userId
     * @return array
     */
    public function getAnswers(int $userId): array
    {
        return AnswerService::getByUserId($userId);
    }

    /**
     * 获取指定用户发表的文章列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getArticles(int $userId): array
    {
        return ArticleService::getByUserId($userId);
    }

    /**
     * 获取指定用户发表的评论列表
     *
     * @param  int      $userId
     * @return array
     */
    public function getComments(int $userId): array
    {
        return CommentService::getByUserId($userId);
    }

    /**
     * 获取我的用户信息
     *
     * @return array
     */
    public function getMine(): array
    {
        return UserService::get(Auth::userId());
    }

    /**
     * 更新我的用户信息
     *
     * @return array
     */
    public function updateMine(): array
    {
        $userId = Auth::userId();

        UserService::update($userId, Request::getParsedBody());

        return UserService::get($userId);
    }

    /**
     * 上传我的头像
     *
     * @return array
     */
    public function uploadMyAvatar(): array
    {
        $userId = Auth::userId();
        $filename = UserAvatarService::upload($userId, Request::getUploadedFiles());

        return UserAvatarService::getBrandUrls($userId, $filename);
    }

    /**
     * 删除我的的头像
     *
     * @return array
     */
    public function deleteMyAvatar(): array
    {
        $userId = Auth::userId();
        $filename = UserAvatarService::delete($userId);

        return UserAvatarService::getBrandUrls($userId, $filename);
    }

    /**
     * 上传我的封面
     *
     * @return array
     */
    public function uploadMyCover(): array
    {
        $userId = Auth::userId();
        $filename = UserCoverService::upload($userId, Request::getUploadedFiles());

        return UserCoverService::getBrandUrls($userId, $filename);
    }

    /**
     * 删除我的封面
     *
     * @return array
     */
    public function deleteMyCover(): array
    {
        $userId = Auth::userId();

        UserCoverService::delete($userId);

        return UserCoverService::getBrandUrls($userId);
    }

    /**
     * 发送注册验证邮件
     *
     * @return array
     */
    public function sendRegisterEmail(): array
    {
        UserService::sendRegisterEmail(Request::getParsedBody());

        return [];
    }

    /**
     * 发送重置密码验证邮件
     *
     * @return array
     */
    public function sendPasswordResetEmail(): array
    {
        UserService::sendPasswordResetEmail(Request::getParsedBody());

        return [];
    }

    /**
     * 重置密码
     *
     * @return array
     */
    public function updatePassword(): array
    {
        UserService::updatePassword(Request::getParsedBody());

        return [];
    }

    /**
     * 获取我的关注者
     *
     * @return array
     */
    public function getMyFollowers(): array
    {
        return UserService::getFollowers(Auth::userId());
    }

    /**
     * 获取我关注的人
     *
     * @return array
     */
    public function getMyFollowees(): array
    {
        return UserService::getFollowing(Auth::userId());
    }

    /**
     * 获取我关注的提问
     *
     * @return array
     */
    public function getMyFollowingQuestions(): array
    {
        return QuestionService::getFollowing(Auth::userId());
    }

    /**
     * 获取当前用户关注的文章列表
     *
     * @return array
     */
    public function getMyFollowingArticles(): array
    {
        return ArticleService::getFollowing(Auth::userId());
    }

    /**
     * 获取我关注的话题
     *
     * @return array
     */
    public function getMyFollowingTopics(): array
    {
        return TopicService::getFollowing(Auth::userId());
    }

    /**
     * 获取当前用户发表的提问列表
     *
     * @return array
     */
    public function getMyQuestions(): array
    {
        return QuestionService::getByUserId(Auth::userId());
    }

    /**
     * 获取当前用户发表的回答列表
     *
     * @return array
     */
    public function getMyAnswers(): array
    {
        return AnswerService::getByUserId(Auth::userId());
    }

    /**
     * 获取当前用户发表的文章列表
     *
     * @return array
     */
    public function getMyArticles(): array
    {
        return ArticleService::getByUserId(Auth::userId());
    }

    /**
     * 获取当前用户发表的评论列表
     *
     * @return array
     */
    public function getMyComments(): array
    {
        return CommentService::getByUserId(Auth::userId());
    }

    /**
     * 批量禁用用户
     *
     * @param array $userIds
     *
     * @return array
     */
    public function disableMultiple(array $userIds): array
    {
        UserService::disableMultiple($userIds);

        return UserService::getMultiple($userIds);
    }

    /**
     * 禁用指定用户，实质上是软删除用户，用户不能物理删除
     *
     * @param  int      $userId
     * @return array
     */
    public function disable(int $userId): array
    {
        UserService::disable($userId);

        return UserService::get($userId);
    }

    /**
     * 批量恢复用户
     *
     * @param array $userIds
     *
     * @return array
     */
    public function enableMultiple(array $userIds): array
    {
        UserService::enableMultiple($userIds);

        return UserService::getMultiple($userIds);
    }

    /**
     * 恢复指定用户
     *
     * @param  int   $userId
     * @return array
     */
    public function enable(int $userId): array
    {
        UserService::enable($userId);

        return UserService::get($userId);
    }
}
