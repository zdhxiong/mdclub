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
     * @return null
     */
    public function readAll()
    {
        NotificationService::readAll();

        return null;
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
     * @return null
     */
    public function deleteAll()
    {
        NotificationService::deleteAll();

        return null;
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
     * @return null
     */
    public function deleteMultiple(array $notificationIds)
    {
        NotificationService::deleteMultiple($notificationIds);

        return null;
    }

    /**
     * 删除
     *
     * @param int $notificationId
     * @return null
     */
    public function delete(int $notificationId)
    {
        NotificationService::delete($notificationId);

        return null;
    }
}
