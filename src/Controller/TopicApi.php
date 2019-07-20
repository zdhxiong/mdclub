<?php

declare(strict_types=1);

namespace MDClub\Controller;

use MDClub\Helper\Request;

/**
 * 话题 restful api
 */
class TopicApi extends Abstracts
{
    /**
     * 获取话题列表
     *
     * @uses TransformTopic
     * @return array
     */
    public function getList(): array
    {
        return $this->topicService->getList();
    }

    /**
     * 创建话题
     *
     * @uses NeedManager
     * @uses TransformTopic
     * @return array
     */
    public function create(): array
    {
        $topicId = $this->topicUpdateService->create(
            $this->request->getParsedBody()['name'] ?? '',
            $this->request->getParsedBody()['description'] ?? '',
            $this->request->getUploadedFiles()['cover'] ?? null
        );

        return $this->topicService->get($topicId);
    }

    /**
     * 批量删除话题
     *
     * @uses NeedManager
     * @return array
     */
    public function deleteMultiple(): array
    {
        $topicIds = Request::getQueryParamToArray($this->request, 'topic_id', 100) ?? [];

        $this->topicService->deleteMultiple($topicIds);

        return [];
    }

    /**
     * 获取指定话题信息
     *
     * @param  int      $topic_id
     * @return array
     */
    public function get(int $topic_id): array
    {
        return $this->topicService->getOrFail($topic_id);
    }

    /**
     * 更新话题
     *
     * @param  int      $topic_id
     * @return array
     */
    public function update(int $topic_id): array
    {
        $this->topicUpdateService->update(
            $topic_id,
            $this->request->getParsedBody()['name'] ?? null,
            $this->request->getParsedBody()['description'] ?? null,
            $this->request->getUploadedFiles()['cover'] ?? null
        );

        return $this->topicService->get($topic_id);
    }

    /**
     * 删除话题
     *
     * @param  int      $topic_id
     * @return array
     */
    public function delete(int $topic_id): array
    {
        $this->topicService->delete($topic_id);

        return [];
    }

    /**
     * 获取指定话题的关注者
     *
     * @param  int      $topic_id
     * @return array
     */
    public function getFollowers(int $topic_id): array
    {
        return $this->topicFollowService->getFollowers($topic_id);
    }

    /**
     * 添加关注
     *
     * @param  int      $topic_id
     * @return array
     */
    public function addFollow(int $topic_id): array
    {
        $this->topicFollowService->addFollow($topic_id);
        $followerCount = $this->topicFollowService->getFollowerCount($topic_id);

        return ['follower_count' => $followerCount];
    }

    /**
     * 取消关注
     *
     * @param  int      $topic_id
     * @return array
     */
    public function deleteFollow(int $topic_id): array
    {
        $this->topicFollowService->deleteFollow($topic_id);
        $followerCount = $this->topicFollowService->getFollowerCount($topic_id);

        return ['follower_count' => $followerCount];
    }

    /**
     * 根据话题ID获取提问列表
     *
     * @param  int      $topic_id
     * @return array
     */
    public function getQuestions(int $topic_id): array
    {
        return $this->questionService->getByTopicId($topic_id);
    }

    /**
     * 根据话题ID获取文章列表
     *
     * @param  int   $topic_id
     * @return array
     */
    public function getArticles(int $topic_id): array
    {
        return $this->articleService->getByTopicId($topic_id);
    }

    /**
     * 获取回收站中的话题列表
     *
     * @uses NeedManager
     * @return array
     */
    public function getDeleted(): array
    {
        return  $this->topicService->getDeleted();
    }

    /**
     * 批量恢复话题
     *
     * @uses NeedManager
     * @return array
     */
    public function restoreMultiple(): array
    {
        $topicIds = Request::getQueryParamToArray($this->request, 'topic_id', 100);

        $this->topicService->restoreMultiple($topicIds);

        return [];
    }

    /**
     * 批量从回收站中删除话题
     *
     * @uses NeedManager
     * @return array
     */
    public function destroyMultiple(): array
    {
        $topicIds = Request::getQueryParamToArray($this->request, 'topic_id', 100);

        $this->topicService->destroyMultiple($topicIds);

        return [];
    }

    /**
     * 恢复指定话题
     *
     * @uses NeedManager
     * @param  int      $topic_id
     * @return array
     */
    public function restore(int $topic_id): array
    {
        $this->topicService->restore($topic_id);

        return [];
    }

    /**
     * 从回收站中删除指定话题
     *
     * @uses NeedManager
     * @param  int      $topic_id
     * @return array
     */
    public function destroy(int $topic_id): array
    {
        $this->topicService->destroy($topic_id);

        return [];
    }
}
