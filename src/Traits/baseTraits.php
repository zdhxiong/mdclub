<?php

declare(strict_types=1);

namespace App\Traits;

use App\Constant\ErrorConstant;
use App\Exception\ApiException;

/**
 * has 和 hasOrFail 方法
 *
 * Trait hasTraits
 * @package App\Traits
 */
trait baseTraits
{
    abstract public function getPrivacyFields(): array;
    abstract public function handle($data): array;
    abstract public function addRelationship(array $items, array $relationship = []): array;

    /**
     * 判断指定对象是否存在
     *
     * @param  int  $id
     * @return bool
     */
    public function has(int $id): bool
    {
        return $this->currentModel->has($id);
    }

    /**
     * 若对象不存在，则抛出异常
     *
     * @param  int  $id
     * @return bool
     */
    public function hasOrFail(int $id): bool
    {
        if (!$isHas = $this->has($id)) {
            $this->throwNotFoundException();
        }

        return $isHas;
    }

    /**
     * 根据对象的ID数组判断这些对象是否存在
     *
     * @param  array $ids
     * @return array      键名为对象ID，键值为bool值
     */
    public function hasMultiple(array $ids): array
    {
        $ids = array_unique($ids);
        $result = [];

        if (!$ids) {
            return $result;
        }

        $existIds = $this->currentModel
            ->where([$this->currentModel->primaryKey => $ids])
            ->pluck($this->currentModel->primaryKey);

        foreach ($ids as $id) {
            $result[$id] = in_array($id, $existIds);
        }

        return $result;
    }

    /**
     * 获取对象信息
     *
     * @param  int   $id
     * @param  bool  $withRelationship
     * @return array
     */
    public function get(int $id, bool $withRelationship = false): array
    {
        $data = $this->currentModel
            ->field($this->getPrivacyFields(), true)
            ->get($id);

        if (!$data) {
            $this->throwNotFoundException();
        }

        $data = $this->handle($data);

        if ($withRelationship) {
            $data = $this->addRelationship($data);
        }

        return $data;
    }

    /**
     * 获取多个对象信息
     *
     * @param  array  $ids
     * @param  bool   $withRelationship
     * @return array
     */
    public function getMultiple(array $ids, bool $withRelationship = false): array
    {
        if (!$ids) {
            return [];
        }

        $items = $this->currentModel
            ->where([$this->currentModel->primaryKey => $ids])
            ->field($this->getPrivacyFields(), true)
            ->select();

        foreach ($items as &$item) {
            $item = $this->handle($item);
        }

        if ($withRelationship) {
            $items = $this->addRelationship($items);
        }

        return $items;
    }

    /**
     * 抛出对象不存在的异常
     */
    protected function throwNotFoundException()
    {
        $exceptions = [
            'answer'   => ErrorConstant::ANSWER_NOT_FOUND,
            'article'  => ErrorConstant::ARTICLE_NOT_FOUND,
            'comment'  => ErrorConstant::COMMENT_NOT_FOUND,
            'question' => ErrorConstant::QUESTION_NOT_FOUND,
            'report'   => ErrorConstant::REPORT_NOT_FOUND,
            'topic'    => ErrorConstant::TOPIC_NOT_FOUND,
            'user'     => ErrorConstant::USER_NOT_FOUND,
        ];

        throw new ApiException($exceptions[$this->currentModel->table]);
    }
}
