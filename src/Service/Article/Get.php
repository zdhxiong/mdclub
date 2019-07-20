<?php

declare(strict_types=1);

namespace MDClub\Service\Article;

use MDClub\Library\Collection;
use MDClub\Traits\Getable;

/**
 * 获取文章
 */
class Get extends Abstracts
{
    use Getable;

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
     * 获取允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return ['article_id', 'user_id', 'topic_id']; // topic_id 需要另外写逻辑
    }

    /**
     * 根据 user_id 获取文章列表
     *
     * @param  int              $userId
     * @return array
     */
    public function getByUserId(int $userId)
    {
        $this->userGetService->hasOrFail($userId);

        return $this->model
            ->where('user_id', $userId)
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();
    }

    /**
     * 根据 topic_id 获取文章列表
     *
     * @param  int              $topicId
     * @return array
     */
    public function getByTopicId(int $topicId)
    {
        $this->topicGetService->hasOrFail($topicId);

        return $this->model
            ->join(['[><]topicable' => ['article_id' => 'topicable_id']])
            ->where('topicable.topicable_type', 'article')
            ->where('topicable.topic_id', $topicId)
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();
    }

    /**
     * 获取 where
     *
     * @return array
     */
    protected function getWhereFromQuery(): array
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
    }

    /**
     * 获取已删除的文章列表
     *
     * @return array
     */
    public function getDeleted()
    {
        $defaultOrder = ['delete_time' => 'DESC'];
        $allowOrderFields = collect($this->getAllowOrderFields())->push('delete_time')->unique()->all();
        $order = $this->getOrder($defaultOrder, $allowOrderFields);

        return $this->model
            ->onlyTrashed()
            ->where($this->getWhereFromQuery())
            ->order($order)
            ->paginate();
    }

    /**
     * 获取文章列表
     *
     * @return array
     */
    public function getList()
    {
        return $this->model
            ->where($this->getWhereFromQuery())
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();
    }
}
