<?php

declare(strict_types=1);

namespace MDClub\Service\Interfaces;

/**
 * 可评论对象接口。用于 question, answer, article
 */
interface CommentableInterface
{
    /**
     * 获取指定对象下的评论列表
     *
     * @param  int   $commentableId
     * @return array
     */
    public function getComments(int $commentableId): array;

    /**
     * 发表评论
     *
     * @param  int   $commentableId
     * @param  array $data [content]
     * @return int
     */
    public function addComment(int $commentableId, array $data): int;
}
