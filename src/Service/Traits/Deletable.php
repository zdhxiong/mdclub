<?php

declare(strict_types=1);

namespace MDClub\Service\Traits;

use MDClub\Facade\Service\NotificationService;
use MDClub\Model\Abstracts as ModelAbstracts;

/**
 * 可删除的对象，含软删除功能。用于 answer, comment, article, question, topic
 *
 * 放入回收站需要发送通知
 * 未在回收站中直接删除需要发送通知
 * 已在回收站中直接删除不发送通知
 *
 * 父类中可使用：
 * afterDelete(array $items): void
 * afterTrash(array $items): void
 * afterUntrash(array $items): void
 */
trait Deletable
{
    /**
     * @inheritDoc
     */
    abstract public function getModelInstance(): ModelAbstracts;

    /**
     * 抛出资源不存在异常
     */
    abstract protected function throwNotFoundException(): void;

    /**
     * 检查删除当前对象时是否需要发送通知
     *
     * @return bool
     */
    private function isNeedSendNotification(): bool
    {
        $model = $this->getModelInstance();
        $needNotifications = [
            'question',
            'article',
            'answer',
            'comment',
        ];

        return in_array($model->table, $needNotifications);
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(array $deletableIds): void
    {
        $model = $this->getModelInstance();
        $primaryKey = $model->primaryKey;

        $items = $model
            ->force()
            ->where($primaryKey, $deletableIds)
            ->select();

        if (!$items) {
            return;
        }

        $model->force()->delete(array_column($items, $primaryKey));

        if ($this->isNeedSendNotification()) {
            foreach ($items as $item) {
                if (!$item['delete_time']) {
                    NotificationService::add($item['user_id'], "{$model->table}_deleted", [
                        "{$model->table}_id" => $item[$primaryKey],
                    ]);
                }
            }

            NotificationService::send();
        }

        if (method_exists($this, 'afterDelete')) {
            $this->afterDelete($items);
        }
    }

    /**
     * @inheritDoc
     */
    abstract public function delete(int $deletableId): void;

    /**
     * 永久删除，无论是否在回收站中
     *
     * 需要自行实现 delete 方法，在调用该方法前，进行权限验证
     *
     * @param int   $deletableId
     * @param array $item 已通过 $deletableId 查询到的数据，如果传入了该参数，则不再重复查询
     */
    protected function traitDelete(int $deletableId, array $item = null): void
    {
        $model = $this->getModelInstance();

        if (!$item) {
            $item = $model->force()->get($deletableId);
        }

        if (!$item) {
            return;
        }

        $model->force()->delete($deletableId);

        // 不在回收站中才需要发送通知
        if (!$item['delete_time'] && $this->isNeedSendNotification()) {
            NotificationService::add($item['user_id'], "{$model->table}_deleted", [
                "{$model->table}_id" => $deletableId,
            ])->send();
        }

        if (method_exists($this, 'afterDelete')) {
            $this->afterDelete([$item]);
        }
    }

    /**
     * @inheritDoc
     */
    public function trashMultiple(array $deletableIds): array
    {
        $model = $this->getModelInstance();
        $primaryKey = $model->primaryKey;
        $existItems = $model->get($deletableIds);
        $existIds = array_column($existItems, $primaryKey);

        if (!$existItems) {
            return [];
        }

        $model->delete($existIds);

        if ($this->isNeedSendNotification()) {
            foreach ($existItems as $item) {
                NotificationService::add($item['user_id'], "{$model->table}_deleted", [
                    "{$model->table}_id" => $item[$primaryKey],
                ]);
            }

            NotificationService::send();
        }

        $trashedItems = $model->force()->select($existIds);

        if (method_exists($this, 'afterTrash')) {
            $this->afterTrash($trashedItems);
        }

        return $trashedItems;
    }

    /**
     * @inheritDoc
     */
    public function trash(int $id): array
    {
        $model = $this->getModelInstance();
        $item = $model->get($id);

        if (!$item) {
            $this->throwNotFoundException();
        }

        $model->delete($id);

        if ($this->isNeedSendNotification()) {
            NotificationService::add($item['user_id'], "{$model->table}_deleted", [
                "{$model->table}_id" => $id,
            ])->send();
        }

        $trashedItem = $model->force()->get($id);

        if (method_exists($this, 'afterTrash')) {
            $this->afterTrash([$trashedItem]);
        }

        return $trashedItem;
    }

    /**
     * @inheritDoc
     */
    public function untrashMultiple(array $deletableIds): array
    {
        $model = $this->getModelInstance();
        $primaryKey = $model->primaryKey;

        $existIds = $model
            ->onlyTrashed()
            ->where($primaryKey, $deletableIds)
            ->pluck($primaryKey);

        if (!$existIds) {
            return [];
        }

        $model->restore($existIds);

        $untrashedItems = $model->select($existIds);

        if (method_exists($this, 'afterUntrash')) {
            $this->afterUntrash($untrashedItems);
        }

        return $untrashedItems;
    }

    /**
     * @inheritDoc
     */
    public function untrash(int $id): array
    {
        $model = $this->getModelInstance();
        $exist = $model->onlyTrashed()->has($id);

        if (!$exist) {
            $this->throwNotFoundException();
        }

        $model->restore($id);

        $untrashedItem = $model->get($id);

        if (method_exists($this, 'afterUntrash')) {
            $this->afterUntrash([$untrashedItem]);
        }

        return $untrashedItem;
    }
}
