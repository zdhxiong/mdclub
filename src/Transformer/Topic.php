<?php

declare(strict_types=1);

namespace MDClub\Transformer;

/**
 * 话题转换器
 *
 * @property-read \MDClub\Model\Topic         $topicModel
 * @property-read \MDClub\Service\TopicUpdate $topicUpdateService
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
            $item['cover'] = $this->topicUpdateService->getBrandUrls($item['topic_id'], $item['cover']);
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

        $topicable = $this->topicModel
            ->join([
                '[><]topicable' => ['topic_id' => 'topic_id']
            ])
            ->where('topicable.topicable_type', $targetType)
            ->where('topicable.topicable_id', $targetIds)
            ->order('topicable.create_time')
            ->field(['topic.topic_id', 'topic.name', 'topic.cover', 'topicable.topicable_id'])
            ->select();

        return collect($topicable)
            ->keyBy('topicable_id')
            ->unionFill($targetIds)
            ->all();
    }
}
