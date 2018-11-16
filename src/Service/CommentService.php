<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Traits\baseTraits;
use App\Traits\VotableTraits;

/**
 * 评论
 *
 * @property-read \App\Model\CommentModel      currentModel
 *
 * Class CommentService
 * @package App\Service
 */
class CommentService extends ServiceAbstracts
{
    use baseTraits, VotableTraits;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return ['delete_time'];
    }

    /**
     * 获取允许排序的字段
     *
     * @return array
     */
    public function getAllowOrderFields(): array
    {
        return ['vote_count', 'create_time', 'update_time'];
    }

    public function handle($data): array
    {
        return $data;
    }

    /**
     * 为评论添加 relationship 字段
     * {
     *     user: {
     *         user_id: '',
     *         username: '',
     *         headline: '',
     *         avatar: {
     *             s: '',
     *             m: '',
     *             l: ''
     *         }
     *     },
     *     question: {},
     *     answer: {},
     *     article: {},
     *     voting: up、down、false
     * }
     *
     * @param  array $comments
     * @return array
     */
    public function addRelationship(array $comments): array
    {
        return $comments;
    }
}
