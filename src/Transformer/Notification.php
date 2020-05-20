<?php

declare(strict_types=1);

namespace MDClub\Transformer;

/**
 * 通知转换器
 */
class Notification extends Abstracts
{
    protected $table = 'notification';
    protected $primaryKey = 'notification_id';
    protected $availableIncludes = ['receiver', 'sender', 'article', 'question', 'answer', 'comment', 'reply'];

    /**
     * 添加 receiver 子资源
     *
     * @param array $items
     * @return array
     */
    protected function receiver(array $items): array
    {
        return $this->relationshipItemTransform($items, 'receiver', User::class);
    }

    /**
     * 获取 sender 子资源
     *
     * @param array $items
     * @return array
     */
    protected function sender(array $items): array
    {
        return $this->relationshipItemTransform($items, 'sender', User::class);
    }

    /**
     * 获取 reply 子资源
     *
     * @param array $items
     * @return array
     */
    protected function reply(array $items): array
    {
        return $this->relationshipItemTransform($items, 'reply', Comment::class);
    }
}
