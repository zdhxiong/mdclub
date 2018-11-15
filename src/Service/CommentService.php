<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\ServiceAbstracts;
use App\Constant\ErrorConstant;
use App\Exception\ApiException;

/**
 * 评论
 *
 * Class CommentService
 * @package App\Service
 */
class CommentService extends ServiceAbstracts
{
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

    /**
     * 获取评论详情
     *
     * @param  int   $commentId
     * @param  bool  $withRelationship
     * @return array
     */
    public function get(int $commentId, bool $withRelationship): array
    {
        $comment = $this->commentModel
            ->field($this->getPrivacyFields(), true)
            ->get($commentId);

        if (!$comment) {
            throw new ApiException(ErrorConstant::COMMENT_NOT_FOUNT);
        }

        if ($withRelationship) {
            $comment = $this->addRelationship($comment);
        }

        return $comment;
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
        if (!$commentIds) {
            return [];
        }

        $comments = $this->commentModel
            ->where(['comment_id' => $commentIds])
            ->field($this->getPrivacyFields(), true)
            ->select();

        if ($withRelationship) {
            $comments = $this->addRelationship($comments);
        }

        return $comments;
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
