<?php

declare(strict_types=1);

namespace MDClub\Transformer;

/**
 * 评论转换器
 *
 * @property-read \MDClub\Model\Comment $commentModel
 */
class Comment extends Abstracts
{
    protected $table = 'comment';
    protected $primaryKey = 'comment_id';
    protected $availableIncludes = ['user', 'voting'];
    protected $userExcept = ['delete_time'];

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

        $comments = $this->commentModel
            ->field(['comment_id', 'content', 'create_time', 'update_time'])
            ->select($commentIds);

        return collect($comments)
            ->keyBy('comment_id')
            ->map(static function ($item) {
                $item['content_summary'] = mb_substr($item['content'], 0, 80);

                return $item;
            })
            ->unionFill($commentIds)
            ->all();
    }
}
