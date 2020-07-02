<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Model\TopicModel;
use MDClub\Facade\Service\TopicService;
use MDClub\Initializer\Collection;

/**
 * 话题转换器
 */
class Topic extends Abstracts
{
    protected $table = 'topic';
    protected $primaryKey = 'topic_id';
    protected $availableIncludes = ['is_following'];
    protected $userExcept = ['delete_time'];

    /**
     * 格式化话题信息
     *
     * @param  array $item
     * @return array
     */
    protected function format(array $item): array
    {
        if (isset($item['topic_id'], $item['cover'])) {
            $item['cover'] = TopicService::getBrandUrls($item['topic_id'], $item['cover']);
        }

        if (isset($item['name'])) {
            $item['name'] = htmlspecialchars($item['name']);
        }

        if (isset($item['description'])) {
            $item['description'] = htmlspecialchars($item['description']);
        }

        return $item;
    }

    /**
     * 获取 topic 子资源
     *
     * @param  array  $targetIds
     * @param  string $targetType
     * @return array
     */
    public function getInRelationship(array $targetIds, string $targetType): array
    {
        if (!$targetIds) {
            return [];
        }

        $topicable = TopicModel
            ::join([
                '[><]topicable' => ['topic_id' => 'topic_id']
            ])
            ->where('topicable.topicable_type', $targetType)
            ->where('topicable.topicable_id', $targetIds)
            ->order('topicable.create_time')
            ->field(['topic.topic_id', 'topic.name', 'topic.cover', 'topicable.topicable_id'])
            ->select();

        return collect($topicable)
            ->map(function ($item) {
                $item['cover'] = TopicService::getBrandUrls($item['topic_id'], $item['cover']);
                $item['name'] = htmlspecialchars($item['name']);

                return $item;
            })
            ->groupBy('topicable_id')
            ->map(function (Collection $items) {
                // 移除 topicable_id
                return $items->map(function ($item) {
                    unset($item['topicable_id']);

                    return $item;
                });
            })
            ->unionFill($targetIds)
            ->all();
    }
}
