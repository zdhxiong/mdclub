<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Model\CommentModel;

/**
 * 评论转换器
 */
class Comment extends Abstracts
{
    protected $table = 'comment';
    protected $primaryKey = 'comment_id';
    protected $availableIncludes = ['user', 'voting'];
    protected $userExcept = ['delete_time'];

    /**
     * 格式化评论信息
     *
     * @param array $item
     * @return array
     */
    protected function format(array $item): array
    {
        if (isset($item['content'])) {
            $item['content'] = htmlentities($item['content']);
        }

        return $item;
    }

    /**
     * 获取 comment 子资源
     *
     * @param  array $commentIds
     * @return array
     */
    public function getInRelationship(array $commentIds): array
    {
        if (!$commentIds) {
            return [];
        }

        $comments = CommentModel::field(['comment_id', 'content', 'create_time', 'update_time'])
            ->select($commentIds);

        return collect($comments)
            ->keyBy('comment_id')
            ->map(static function ($item) {
                $item['content_summary'] = htmlentities(mb_substr($item['content'], 0, 80));
                unset($item['content']);

                return $item;
            })
            ->unionFill($commentIds)
            ->all();
    }
}
