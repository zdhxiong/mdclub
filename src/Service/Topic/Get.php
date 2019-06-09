<?php

declare(strict_types=1);

namespace App\Service\Topic;

use App\Traits\Getable;
use Tightenco\Collect\Support\Collection;

/**
 * 获取话题
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return $this->roleService->managerId()
            ? []
            : ['delete_time'];
    }

    /**
     * 获取允许排序的字段
     *
     * @return array
     */
    public function getAllowOrderFields(): array
    {
        return ['topic_id', 'follower_count'];
    }

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return ['topic_id', 'name'];
    }

    /**
     * 处理话题内容
     *
     * @param  array $topics
     * @return array
     */
    public function addFormatted(array $topics): array
    {
        foreach ($topics as &$topic) {
            if (isset($topic['cover'])) {
                $topic['cover'] = $this->getBrandUrls($topic['topic_id'], $topic['cover']);
            }
        }

        return $topics;
    }

    /**
     * 添加关联信息
     *
     * @param  array $topics
     * @param  array $relationship {is_following: false} 若指定了该参数，则不再查询数据库
     * @return array
     */
    public function addRelationship(array $topics, array $relationship = []): array
    {
        $topicIds = array_unique(array_column($topics, 'topic_id'));

        if (isset($relationship['is_following'])) {
            $followingTopicIds = $relationship['is_following'] ? $topicIds : [];
        } else {
            $followingTopicIds = $this->followService->getInRelationship($topicIds, 'topic');
        }

        foreach ($topics as &$topic) {
            $topic['relationship'] = [
                'is_following' => in_array($topic['topic_id'], $followingTopicIds),
            ];
        }

        return $topics;
    }

    /**
     * 获取已删除的话题列表
     *
     * @return array|Collection
     */
    public function getDeleted()
    {
        $defaultOrder = ['delete_time' => 'DESC'];
        $allowOrderFields = collect($this->getAllowOrderFields())->push('delete_time')->unique()->all();
        $order = $this->getOrder($defaultOrder, $allowOrderFields);

        $this->beforeGet();

        $result = $this->model
            ->onlyTrashed()
            ->where($this->getWhere())
            ->order($order)
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 获取话题列表
     *
     * @return array|Collection
     */
    public function getList()
    {
        $this->beforeGet();

        $result = $this->model
            ->where($this->getWhere())
            ->order($this->getOrder(['topic_id' => 'ASC']))
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 获取在 relationship 中使用的 topics
     *
     * @param  array  $targetIds  对象ID数组
     * @param  string $targetType 对象类型
     * @return array              键名为对象ID，键值为话题信息组成的二维数组
     */
    public function getInRelationship(array $targetIds, string $targetType): array
    {
        $topics = array_combine($targetIds, array_fill(0, count($targetIds), []));

        return $this->model
            ->join([
                '[><]topicable' => ['topic_id' => 'topic_id']
            ])
            ->where('topicable.topicable_type', $targetType)
            ->where('topicable.topicable_id', $targetIds)
            ->order('topicable.create_time')
            ->field(['topic.topic_id', 'topic.name', 'topic.cover', 'topicable.topicable_id'])
            ->fetchCollection()
            ->select()
            ->keyBy('topicable_id')
            ->map(static function ($item) {
                return $this->addFormatted([
                    'topic_id' => $item['topic_id'],
                    'name'     => $item['name'],
                    'cover'    => $item['cover'],
                ]);
            })
            ->union($topics)
            ->all();
    }
}
