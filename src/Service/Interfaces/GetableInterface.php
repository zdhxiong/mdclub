<?php

declare(strict_types=1);

namespace MDClub\Service\Interfaces;

/**
 * 可获取单个项目或列表的对象接口（answer, article, comment, question, report, topic, user）
 */
interface GetableInterface
{
    /**
     * 判断指定对象是否存在
     *
     * @param  int|string  $id
     * @return bool
     */
    public function has($id): bool;

    /**
     * 若对象不存在，则抛出异常
     *
     * @param  int|string  $id
     */
    public function hasOrFail($id): void;

    /**
     * 根据对象的ID数组判断这些对象是否存在
     *
     * @param  array $ids
     * @return array       键名为对象ID，键值为bool值
     */
    public function hasMultiple(array $ids): array;

    /**
     * 获取对象信息
     *
     * @param  int|string  $id
     * @return array|null
     */
    public function get($id): ?array;

    /**
     * 获取对象信息，不存在则抛出异常
     *
     * @param  int|string  $id
     * @return array
     */
    public function getOrFail($id): array;

    /**
     * 获取多个对象信息
     *
     * @param  array $ids
     * @return array
     */
    public function getMultiple(array $ids): array;

    /**
     * 获取对象列表
     *
     * @return array
     */
    public function getList(): array;
}
