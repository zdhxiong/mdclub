<?php

declare(strict_types=1);

namespace App\Service\Image;

use App\Traits\Getable;
use Tightenco\Collect\Support\Collection;

/**
 * 获取图片
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 获取允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return ['hash', 'item_type', 'item_id', 'user_id'];
    }

    /**
     * 对结果中的内容进行处理
     *
     * @param  array $images
     * @return array
     */
    public function addFormatted(array $images): array
    {
        foreach ($images as &$image) {
            if (isset($image['hash'], $image['create_time'])) {
                $image['urls'] = $this->getUrls($image['hash'], $image['create_time']);
            }
        }

        return $images;
    }

    /**
     * 为图片信息添加 relationship 字段
     * {
     *     user: {},
     *     question: {},
     *     answer: {},
     *     article: {}
     * }
     *
     * @param array $images
     * @return array
     */
    public function addRelationship(array $images): array
    {
        $targetIds = []; // 键名为对象类型，键值为对象的ID数组
        $targets = []; // 键名为对象类型，键值为对象ID和对象信息的多维数组
        $targetTypes = ['user', 'question', 'answer', 'article'];

        foreach ($images as $image) {
            $targetIds['user'][] = $image['user_id'];

            if ($image['item_type']) {
                $targetIds[$image['item_type']][] = $image['item_id'];
            }
        }

        foreach ($targetTypes as $type) {
            if (isset($targetIds[$type])) {
                $targetIds[$type] = array_unique($targetIds[$type]);
                $targets[$type] = $this->{$type . 'Service'}->getInRelationship($targetIds[$type]);
            }
        }

        foreach ($images as &$image) {
            $image['relationship']['user'] = $targets['user'][$image['user_id']];
            if ($image['item_type']) {
                $image['relationship'][$image['item_type']] = $targets[$image['item_type']][$image['item_id']];
            }
        }

        return $images;
    }

    /**
     * 获取图片列表
     *
     * @return array|Collection
     */
    public function getList()
    {
        $this->beforeGet();

        $result = $this->model
            ->where($this->getWhere())
            ->order('create_time', 'DESC')
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }
}
