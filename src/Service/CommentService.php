<?php

declare(strict_types=1);

namespace App\Service;

/**
 * 评论
 *
 * Class CommentService
 * @package App\Service
 */
class CommentService extends Service
{
    /**
     * 获取评论详情
     *
     * @param  int   $commentId
     * @param  bool  $withRelationship
     * @return array
     */
    public function get(int $commentId, bool $withRelationship): array
    {

    }

    /**
     * 获取多个评论信息
     *
     * @param  array $commentIds
     * @param  bool  $withRelationship
     * @return array
     */
    public function getMultiple(array $commentIds, bool $withRelationship = false): array
    {

    }
}
