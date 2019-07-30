<?php

declare(strict_types=1);

namespace MDClub\Service\Article;

use MDClub\Traits\Getable;

/**
 * 获取文章
 *
 * @property-read \MDClub\Model\Article $model
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 根据 user_id 获取文章列表
     *
     * @param  int   $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        $this->userGetService->hasOrFail($userId);

        return $this->model->getByUserId($userId);
    }

    /**
     * 根据 topic_id 获取文章列表
     *
     * @param  int              $topicId
     * @return array
     */
    /*public function getByTopicId(int $topicId)
    {
        $this->topicGetService->hasOrFail($topicId);

        return $this->model
            ->join(['[><]topicable' => ['article_id' => 'topicable_id']])
            ->where('topicable.topicable_type', 'article')
            ->where('topicable.topic_id', $topicId)
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();
    }*/

    /**
     * 获取 where
     *
     * @return array
     */
    /*protected function getWhereFromQuery(): array
    {
        $where = $this->getWhere();

        if (isset($where['topic_id'])) {
            $this->model->join(['[><]topicable' => ['article_id' => 'topicable_id']]);

            $where['topicable.topic_id'] = $where['topic_id'];
            $where['topicable.topicable_type'] = 'article';
            unset($where['topic_id']);
        }

        if (isset($where['user_id'])) {
            $where['article.user_id'] = $where['user_id'];
            unset($where['user_id']);
        }

        if (isset($where['article_id'])) {
            $where['article.article_id'] = $where['article_id'];
            unset($where['article_id']);
        }

        return $where;
    }*/

    /**
     * 获取已删除的文章列表
     *
     * @return array
     */
    /*public function getDeleted()
    {
        $defaultOrder = ['delete_time' => 'DESC'];
        $allowOrderFields = collect($this->getAllowOrderFields())->push('delete_time')->unique()->all();
        $order = $this->getOrder($defaultOrder, $allowOrderFields);

        return $this->model
            ->onlyTrashed()
            ->where($this->getWhereFromQuery())
            ->order($order)
            ->paginate();
    }*/

    /**
     * 获取文章列表
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->model->getList();
    }
}
