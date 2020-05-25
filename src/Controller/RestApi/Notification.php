<?php

declare(strict_types=1);

namespace MDClub\Controller\RestApi;

use MDClub\Facade\Service\NotificationService;

/**
 * 通知 API
 */
class Notification extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected function getService(): string
    {
        return \MDClub\Service\Notification::class;
    }

    /**
     * 获取未读通知数量
     *
     * @return array
     */
    public function getCount(): array
    {
        return [
            'notification_count' => NotificationService::getCount(),
        ];
    }

    /**
     * 把所有通知标记为已读
     *
     * @return array
     */
    public function readAll(): array
    {
        NotificationService::readAll();

        return [];
    }

    /**
     * 获取通知列表
     *
     * @return array
     */
    public function getList(): array
    {
        return NotificationService::getList();
    }

    /**
     * 删除所有通知
     *
     * @return array
     */
    public function deleteAll(): array
    {
        NotificationService::deleteAll();

        return [];
    }

    /**
     * 批量标记为已读
     *
     * @param array $notificationIds
     * @return array
     */
    public function readMultiple(array $notificationIds): array
    {
        return NotificationService::readMultiple($notificationIds);
    }

    /**
     * 标记为已读
     *
     * @param int $notificationId
     * @return array
     */
    public function read(int $notificationId): array
    {
        return NotificationService::read($notificationId);
    }

    /**
     * 批量删除
     *
     * @param array $notificationIds
     * @return array
     */
    public function deleteMultiple(array $notificationIds): array
    {
        NotificationService::deleteMultiple($notificationIds);

        return [];
    }

    /**
     * 删除
     *
     * @param int $notificationId
     * @return array
     */
    public function delete(int $notificationId): array
    {
        NotificationService::delete($notificationId);

        return [];
    }
}
