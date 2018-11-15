<?php

declare(strict_types=1);

namespace App\Traits;

use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;

/**
 * 可评论对象 Trait （question、answer、article）
 *
 * Trait CommentableTraits
 * @package App\Traits
 */
trait CommentableTraits
{
    abstract public function hasOrFail(int $id): bool;

    /**
     * 获取评论列表
     *
     * @param  int   $commentableId
     * @param  bool  $withRelationship
     * @return array
     */
    public function getComments(int $commentableId, bool $withRelationship = false): array
    {
        $this->hasOrFail($commentableId);

        $list = $this->commentModel
            ->where([
                'commentable_id'   => $commentableId,
                'commentable_type' => $this->currentModel->table,
            ])
            ->order($this->commentService->getOrder(['create_time' => 'ASC']))
            ->field($this->commentService->getPrivacyFields(), true)
            ->paginate();

        if ($withRelationship) {
            $list['data'] = $this->commentService->addRelationship($list['data']);
        }

        return $list;
    }

    /**
     * 发表评论
     *
     * @param  int    $commentableId
     * @param  string $content
     * @return int                   评论ID
     */
    public function addComment(int $commentableId, string $content = null): int
    {
        $userId = $this->roleService->userIdOrFail();
        $this->hasOrFail($commentableId);

        if ($content) {
            $content = strip_tags($content);
        }

        // 评论最多 1000 个字，最少 1 个字
        $errors = [];
        if (!$content) {
            $errors['content'] = '评论内容不能为空';
        } elseif (!ValidatorHelper::isMax($content, 1000)) {
            $errors['content'] = '评论内容不能超过 1000 个字符';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        return (int)$this->commentModel->insert([
            'commentable_id'   => $commentableId,
            'commentable_type' => $this->currentModel->table,
            'user_id'          => $userId,
            'content'          => $content,
        ]);
    }
}
