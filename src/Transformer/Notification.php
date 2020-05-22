<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Transformer\AnswerTransformer;
use MDClub\Facade\Transformer\ArticleTransformer;
use MDClub\Facade\Transformer\CommentTransformer;
use MDClub\Facade\Transformer\QuestionTransformer;

/**
 * 通知转换器
 */
class Notification extends Abstracts
{
    protected $table = 'notification';
    protected $primaryKey = 'notification_id';
    protected $availableIncludes = ['receiver', 'sender', 'article', 'question', 'answer', 'comment', 'reply'];

    /**
     * 格式化通知信息
     *
     * @param array $item
     * @return array
     */
    protected function format(array $item): array
    {
        if (isset($item['content_deleted']) && $item['content_deleted']) {
            $contentDeleted = unserialize($item['content_deleted']);

            switch ($item['type']) {
                case 'question_deleted':
                    $item['content_deleted'] = QuestionTransformer::transform($contentDeleted, [], false);
                    break;

                case 'article_deleted':
                    $item['content_deleted'] = ArticleTransformer::transform($contentDeleted, [], false);
                    break;

                case 'answer_deleted':
                    $item['content_deleted'] = AnswerTransformer::transform($contentDeleted, [], false);
                    break;

                case 'comment_deleted':
                    $item['content_deleted'] = CommentTransformer::transform($contentDeleted, [], false);
                    break;

                default:
                    break;
            }
        }

        return $item;
    }

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
