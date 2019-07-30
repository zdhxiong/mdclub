<?php

declare(strict_types=1);

namespace MDClub\Transformer;

/**
 * 图片转换器
 *
 * @property-read \MDClub\Model\Image       $imageModel
 * @property-read \MDClub\Service\Image\Get $imageGetService
 */
class Image extends Abstracts
{
    protected $table = 'image';
    protected $primaryKey = 'key';
    protected $availableIncludes = ['user', 'question', 'article', 'answer'];

    /**
     * 格式化图片信息
     *
     * @param  array $item
     * @return array
     */
    protected function format(array $item): array
    {
        if (isset($item['key'], $item['create_time'])) {
            $item['urls'] = $this->imageGetService->getUrls($item['key'], $item['create_time']);
        }

        return $item;
    }

    /**
     * 处理 question, article, answer 子资源
     *
     * @param  array  $items
     * @param  string $itemType
     * @return array
     */
    private function handle(array $items, string $itemType): array
    {
        $ids = [];

        foreach ($items as $item) {
            if ($item['item_type'] === $itemType && !in_array($item['item_id'], $ids)) {
                $ids[] = $item['item_id'];
            }
        }

        $targets = $this->{$itemType . 'Transformer'}->getInRelationship($ids);

        foreach ($items as &$item) {
            if ($item['item_type'] === $itemType) {
                $item['relationship'][$itemType] = $targets[$item['item_id']];
            }
        }

        return $items;
    }

    /**
     * 添加 question 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function question(array $items): array
    {
        return $this->handle($items, 'question');
    }

    /**
     * 添加 article 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function article(array $items): array
    {
        return $this->handle($items, 'article');
    }

    /**
     * 添加 answer 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function answer(array $items): array
    {
        return $this->handle($items, 'answer');
    }

    /**
     * 获取 image 子资源
     *
     * @param array $keys
     * @return array
     */
    protected function getInRelationship(array $keys): array
    {
        if (!$keys) {
            return [];
        }

        $images = $this->imageModel->select($keys);

        return collect($images)
            ->keyBy('key')
            ->map(function ($item) {
                $item['urls'] = $this->imageGetService->getUrls($item['key'], $item['create_time']);

                return $item;
            })
            ->unionFill($keys)
            ->all();
    }
}
