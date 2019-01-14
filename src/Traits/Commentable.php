<?php

declare(strict_types=1);

namespace App\Traits;

use App\Exception\ValidationException;
use App\Helper\ValidatorHelper;

/**
 * 可评论对象 （question、answer、article）
 *
 * Trait Commentable
 * @package App\Traits
 */
trait Commentable
{
    abstract public function hasOrFail(int $id);

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

        $list = $this->container->commentModel
            ->where([
                'commentable_id'   => $commentableId,
                'commentable_type' => $this->currentModel->table,
            ])
            ->order($this->container->commentService->getOrder(['create_time' => 'ASC']))
            ->field($this->container->commentService->getPrivacyFields(), true)
            ->paginate();

        if ($withRelationship) {
            $list['data'] = $this->container->commentService->addRelationship($list['data']);
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
        $userId = $this->container->roleService->userIdOrFail();
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

        $commentId = (int)$this->container->commentModel->insert([
            'commentable_id'   => $commentableId,
            'commentable_type' => $this->currentModel->table,
            'user_id'          => $userId,
            'content'          => $content,
        ]);

        $this->currentModel
            ->where([$this->currentModel->table . '_id' => $commentableId])
            ->update(['comment_count[+]' => 1]);

        return $commentId;
    }
}
