<?php

declare(strict_types=1);

namespace MDClub\Traits;

use MDClub\Exception\ValidationException;
use MDClub\Helper\Validator;

/**
 * 可评论对象。用于 question, answer, article
 *
 * @property-read \MDClub\Library\Auth       $auth
 * @property-read \MDClub\Model\Abstracts    $model
 * @property-read \MDClub\Model\Comment      $commentModel
 */
trait Commentable
{
    /**
     * 获取指定对象下的评论列表
     *
     * @param  int   $commentableId
     * @return array
     */
    public function getComments(int $commentableId): array
    {
        $table = $this->model->table;

        $this->{"${table}GetService"}->hasOrFail($commentableId);

        return $this->commentModel->getByCommentableId($this->model->table, $commentableId);
    }

    /**
     * 发表评论
     *
     *
     * @param  int    $commentableId
     * @param  string $content
     * @return int                    评论ID
     */
    public function addComment(int $commentableId, string $content = null): int
    {
        $userId = $this->auth->userId();
        $table = $this->model->table;

        $this->{"${table}GetService"}->hasOrFail($commentableId);

        // 验证 content
        $content = strip_tags($content);
        $errors = [];

        if (!$content) {
            $errors['content'] = '评论内容不能为空';
        } elseif (!Validator::isMax($content, 1000)) {
            $errors['content'] = '评论内容不能超过 1000 个字符';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

        // 新增评论
        $commentId = (int) $this->commentModel->insert([
            'commentable_type' => $table,
            'commentable_id' => $commentableId,
            'user_id' => $userId,
            'content' => $content,
        ]);

        // 评论数量 +1
        $this->model
            ->where("${table}_id", $commentableId)
            ->inc('comment_count')
            ->update();

        return $commentId;
    }
}
