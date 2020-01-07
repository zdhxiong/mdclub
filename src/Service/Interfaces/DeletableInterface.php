<?php

declare(strict_types=1);

namespace MDClub\Service\Interfaces;

/**
 * 可删除的对象，含软删除功能。用于 answer, comment, article, question, topic
 */
interface DeletableInterface
{
    /**
     * 永久批量删除，无论是否在回收站中
     *
     * @param array $deletableIds
     */
    public function deleteMultiple(array $deletableIds): void;

    /**
     * 永久删除，无论是否在回收站中
     *
     * @param int   $deletableId
     */
    public function delete(int $deletableId): void;

    /**
     * 批量放入回收站
     *
     * @param array $deletableIds
     *
     * @return array 成功放入回收站的资源的数组
     */
    public function trashMultiple(array $deletableIds): array;

    /**
     * 放入回收站
     *
     * @param int $deletableId
     *
     * @return array 成功放入回收站的资源信息
     */
    public function trash(int $deletableId): array;

    /**
     * 批量移出回收站
     *
     * @param array $deletableIds
     *
     * @return array 成功移出回收站的资源的数组
     */
    public function untrashMultiple(array $deletableIds): array;

    /**
     * 移出回收站
     *
     * @param int $deletableId
     *
     * @return array 成功移出回收站的资源的信息
     */
    public function untrash(int $deletableId): array;
}
