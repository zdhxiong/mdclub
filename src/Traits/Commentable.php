<?php

declare(strict_types=1);

namespace App\Traits;

use Tightenco\Collect\Support\Collection;

/**
 * 可评论对象。用于 question, answer, article
 *
 * @property-read \App\Abstracts\ModelAbstracts $model
 * @property-read \App\Service\Comment\Get      $commentGetService
 * @property-read \App\Service\Comment\Update   $commentUpdateService
 * @property-read \App\Service\Role             $roleService
 * @property-read \App\Model\Comment            $commentModel
 */
trait Commentable
{
    protected $isForApi = false;

    /**
     * @return Commentable
     */
    public function forApi(): self
    {
        $this->isForApi = true;

        return $this;
    }

    /**
     * 获取评论列表
     *
     * @param  int              $commentableId
     * @return array|Collection
     */
    public function getList(int $commentableId)
    {
        $this->{$this->model->table . 'GetService'}->hasOrFail($commentableId);

        if ($this->isForApi) {
            $this->commentGetService->forApi();
            $this->isForApi = false;
        }

        $this->commentGetService->beforeGet();

        $result = $this->commentModel
            ->where('commentable_type', $this->model->table)
            ->where('commentable_id', $commentableId)
            ->order($this->commentGetService->getOrder(['create_time' => 'DESC']))
            ->paginate();

        $result = $this->commentGetService->afterGet($result);

        return $this->commentGetService->returnArray($result);
    }

    /**
     * 发表评论
     *
     * @param  int    $commentableId
     * @param  string $content
     * @return int                    评论ID
     */
    public function add(int $commentableId, string $content = null): int
    {
        $userId = $this->roleService->userIdOrFail();
        $this->{$this->model->table . 'GetService'}->hasOrFail($commentableId);

        $content = $this->commentUpdateService->validContent($content);

        $commentId = (int) $this->commentModel->insert([
            'commentable_type' => $this->model->table,
            'commentable_id'   => $commentableId,
            'user_id'          => $userId,
            'content'          => $content,
        ]);

        $this->model
            ->where($this->model->table . '_id', $commentableId)
            ->inc('comment_count')
            ->update();

        return $commentId;
    }
}
