<?php

declare(strict_types=1);

namespace App\Service;

use App\Interfaces\FollowableInterface;

/**
 * 话题
 *
 * Class TopicService
 * @package App\Service
 */
class TopicService extends Service implements FollowableInterface
{
    /**
     * 获取隐私字段
     *
     * @return array
     */
    protected function getPrivacyFields(): array
    {
        return $this->roleService->managerId()
            ? []
            : [
                'delete_time',
            ];
    }

    /**
     * 获取允许排序的字段
     *
     * @return array
     */
    protected function getAllowOrderFields(): array
    {
        return $this->roleService->managerId()
            ? [
                'topic_id',
                'article_count',
                'question_count',
                'follower_count',
                'delete_time',
            ]
            : [];
    }

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    protected function getAllowFilterFields(): array
    {
        return $this->roleService->managerId()
            ? [
                'topic_id',
                'name',
            ]
            : [];
    }

    /**
     * 判断指定话题是否存在
     *
     * @param  int  $topicId
     * @return bool
     */
    public function has(int $topicId): bool
    {
        return $this->topicModel->has($topicId);
    }

    /**
     * 根据话题ID的数组判断这些话题是否存在
     *
     * @param  array $topicIds 话题ID数组
     * @return array           键名为话题ID，键值为bool值
     */
    public function hasMultiple(array $topicIds): array
    {
        $topicIds = array_unique($topicIds);
        $result = [];

        if (!$topicIds) {
            return $result;
        }

        $existTopicIds = $this->topicModel
            ->where(['topic_id' => $topicIds])
            ->pluck('topic_id');

        foreach ($topicIds as $topicId) {
            $result[$topicId] = in_array($topicId, $existTopicIds);
        }

        return $result;
    }

    /**
     * 获取多个话题信息
     *
     * @param  array $topicIds
     * @param  bool  $withRelationship
     * @return array
     */
    public function getMultiple(array $topicIds, bool $withRelationship = false): array
    {
        if (!$topicIds) {
            return [];
        }

        $excludeFields = $this->getPrivacyFields();
        $topics = $this->topicModel
            ->where(['topic_id' => $topicIds])
            ->field($excludeFields, true)
            ->select();

        foreach ($topics as &$topic) {
            $topic = $this->handle($topic);
        }

        return $topics;
    }

    /**
     * 对数据库中读取的话题数据进行处理
     *
     * @param  array $topicInfo
     * @return array
     */
    public function handle(array $topicInfo): array
    {
        if (!$topicInfo) {
            return $topicInfo;
        }

        // todo
        isset($topicInfo['cover']) && $topicInfo['cover'] = '';

        return $topicInfo;
    }

    /**
     * 为话题信息添加 relationship 字段
     * {
     *     is_following: false  登录用户是否已关注该话题
     * }
     *
     * @param  array $topics
     * @param  array $relationship {is_following: false} 若指定了该参数，则不再查询数据库
     * @return array
     */
    public function addRelationship(array $topics, array $relationship = []): array
    {
        if (!$topics) {
            return $topics;
        }

        if (!$isArray = is_array(current($topics))) {
            $topics = [$topics];
        }

        $currentUserId = $this->roleService->userId();
        $topicIds = array_unique(array_column($topics, 'topic_id'));
        $followingTopicIds = [];

        if ($currentUserId) {
            if (isset($relationship['is_following'])) {
                $followingTopicIds = $relationship['is_following'] ? $topicIds : [];
            } else {
                $followingTopicIds = $this->followableModel->where([
                    'user_id'         => $currentUserId,
                    'followable_id'   => $topicIds,
                    'followable_type' => 'topic',
                ])->pluck('topic_id');
            }
        }

        foreach ($topics as &$topic) {
            $topic['relationship'] = [
                'is_following' => in_array($topic['topic_id'], $followingTopicIds),
            ];
        }

        if ($isArray) {
            return $topics;
        }

        return $topics[0];
    }
}
