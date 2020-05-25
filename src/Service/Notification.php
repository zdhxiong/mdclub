<?php

declare(strict_types=1);

namespace MDClub\Service;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Request;
use MDClub\Service\Interfaces\GetableInterface;
use MDClub\Service\Traits\Getable;

/**
 * 通知服务
 */
class Notification extends Abstracts implements GetableInterface
{
    use Getable;

    /**
     * 需要发送的通知数组
     *
     * @var array
     */
    private $notifications = [];

    /**
     * @inheritDoc
     */
    protected function getModel(): string
    {
        return \MDClub\Model\Notification::class;
    }

    /**
     * 获取未读通知数量
     *
     * @return int
     */
    public function getCount(): int
    {
        $model = $this->getModelInstance();
        $defaultFilter = [
            'receiver_id' => Auth::userId(),
            'read' => false,
        ];

        return $model
            ->where($model->getWhereFromRequest($defaultFilter))
            ->count();
    }

    /**
     * 把所有通知标记为已读
     */
    public function readAll(): void
    {
        $model = $this->getModelInstance();

        $model
            ->where($model->getWhereFromRequest())
            ->set('read_time', Request::time())
            ->update();
    }

    /**
     * @inheritDoc
     */
    public function getList(): array
    {
        $defaultFilter = ['receiver_id' => Auth::userId()];
        $model = $this->getModelInstance();

        return $model
            ->where($model->getWhereFromRequest($defaultFilter))
            ->order('create_time', 'DESC')
            ->paginate();
    }

    /**
     * 删除所有通知
     */
    public function deleteAll(): void
    {
        $model = $this->getModelInstance();

        $model
            ->where($model->getWhereFromRequest())
            ->delete();
    }

    /**
     * 批量标记为已读
     *
     * @param array $notificationIds
     * @return array
     */
    public function readMultiple(array $notificationIds): array
    {
        $model = $this->getModelInstance();
        $primaryKey = $model->primaryKey;
        $defaultFilter = $this->defaultFilter();

        $existIds = $model
            ->where($primaryKey, $notificationIds)
            ->where($defaultFilter)
            ->pluck($primaryKey);

        if (!$existIds) {
            return [];
        }

        $model
            ->where($primaryKey, $existIds)
            ->set('read_time', Request::time())
            ->update();

        return $model->select($existIds);
    }

    /**
     * 标记为已读
     *
     * @param int $notificationId
     * @return array
     */
    public function read(int $notificationId): array
    {
        $this->hasOrFail($notificationId);

        $model = $this->getModelInstance();
        $primaryKey = $model->primaryKey;

        $model
            ->where($primaryKey, $notificationId)
            ->set('read_time', Request::time())
            ->update();

        return $model->get($notificationId);
    }

    /**
     * 批量删除通知
     *
     * @param array $notificationIds
     */
    public function deleteMultiple(array $notificationIds): void
    {
        $model = $this->getModelInstance();
        $primaryKey = $model->primaryKey;
        $defaultFilter = $this->defaultFilter();

        $notifications = $model
            ->where($primaryKey, $notificationIds)
            ->where($defaultFilter)
            ->select();

        if (!$notifications) {
            return;
        }

        $model->delete(array_column($notifications, $primaryKey));
    }

    /**
     * 删除通知
     *
     * @param int $notificationId
     */
    public function delete(int $notificationId): void
    {
        $model = $this->getModelInstance();
        $notification = $this->get($notificationId);

        if (!$notification) {
            return;
        }

        $model->delete($notificationId);
    }

    /**
     * 添加需要发送的通知，调用 send 方法后发送
     *
     * @param int $receiverId 接收者ID
     * @param string $type 通知类型
     *                     question_answered
     *                     question_commented
     *                     question_deleted
     *                     article_commented
     *                     article_deleted
     *                     answer_commented
     *                     answer_deleted
     *                     comment_replied
     *                     comment_deleted
     * @param array $relationshipIds 通知相关的ID
     *                               article_id
     *                               question_id
     *                               answer_id
     *                               comment_id
     *                               reply_id
     *
     * @return $this
     */
    public function add(int $receiverId, string $type, array $relationshipIds = []): self
    {
        $senderId = Auth::userId();

        // 用户自己不给自己发通知
        if ($receiverId === $senderId) {
            return $this;
        }

        $data = [
            'receiver_id' => $receiverId,
            'sender_id' => $senderId,
            'type' => $type,
        ];

        $data = array_merge($data, $relationshipIds);

        $this->notifications[] = $data;

        return $this;
    }

    /**
     * 发送通知
     */
    public function send(): void
    {
        if (!$this->notifications) {
            return;
        }

        $model = $this->getModelInstance();
        $model->insert($this->notifications);

        $this->notifications = [];
    }
}
