<?php

declare(strict_types=1);

namespace App\Service\Article;

use App\Traits\Getable;
use Tightenco\Collect\Support\Collection;

/**
 * 获取文章
 */
class Get extends Abstracts
{
    use Getable;

    /**
     * 获取隐私字段
     *
     * @return array
     */
    public function getPrivacyFields(): array
    {
        return $this->roleService->managerId()
            ? []
            : ['delete_time'];
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
     * 获取允许搜索的字段
     *
     * @return array
     */
    public function getAllowFilterFields(): array
    {
        return ['article_id', 'user_id', 'topic_id']; // topic_id 需要另外写逻辑
    }

    /**
     * 对结果中的内容进行处理
     *
     * @param  array $articles
     * @return array
     */
    public function addFormatted(array $articles): array
    {
        return $articles;
    }

    /**
     * 为文章添加 relationship 字段
     * {
     *     user: {}
     *     topics: [ {}, {}, {} ]
     *     is_following: false
     *     voting: up, down, ''
     * }
     *
     * @param  array $articles
     * @param  array $knownRelationship ['is_following': bool]
     * @return array
     */
    public function addRelationship(array $articles, array $knownRelationship = []): array
    {
        $articleIds = array_unique(array_column($articles, 'article_id'));
        $userIds = array_unique(array_column($articles, 'user_id'));

        if (isset($knownRelationship['is_following'])) {
            $followingArticleIds = $knownRelationship['is_following'] ? $articleIds : [];
        } else {
            $followingArticleIds = $this->followService->getInRelationship($articleIds, 'article');
        }

        $votings = $this->voteService->getInRelationship($articleIds, 'article');
        $users = $this->userGetService->getInRelationship($userIds);
        $topics = $this->topicGetService->getInRelationship($articleIds, 'article');

        foreach ($articles as &$article) {
            $article['relationship'] = [
                'user'         => $users[$article['user_id']],
                'topics'       => $topics[$article['article_id']],
                'is_following' => in_array($article['article_id'], $followingArticleIds, true),
                'voting'       => $votings[$article['article_id']],
            ];
        }

        return $articles;
    }

    /**
     * 根据 user_id 获取文章列表
     *
     * @param  int              $userId
     * @return array|Collection
     */
    public function getByUserId(int $userId)
    {
        $this->userGetService->hasOrFail($userId);

        $this->beforeGet();

        $result = $this->model
            ->where('user_id', $userId)
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 根据 topic_id 获取文章列表
     *
     * @param  int              $topicId
     * @return array|Collection
     */
    public function getByTopicId(int $topicId)
    {
        $this->topicGetService->hasOrFail($topicId);

        $this->beforeGet();

        $result = $this->model
            ->join(['[><]topicable' => ['article_id' => 'topicable_id']])
            ->where('topicable.topicable_type', 'article')
            ->where('topicable.topic_id', $topicId)
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
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
     * @return array|Collection
     */
    public function getDeleted()
    {
        $defaultOrder = ['delete_time' => 'DESC'];
        $allowOrderFields = collect($this->getAllowOrderFields())->push('delete_time')->unique()->all();
        $order = $this->getOrder($defaultOrder, $allowOrderFields);

        $this->beforeGet();

        $result = $this->model
            ->onlyTrashed()
            ->where($this->getWhereFromQuery())
            ->order($order)
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 获取文章列表
     *
     * @return array|Collection
     */
    public function getList()
    {
        $this->beforeGet();

        $result = $this->model
            ->where($this->getWhereFromQuery())
            ->order($this->getOrder(['update_time' => 'DESC']))
            ->paginate();

        $result = $this->afterGet($result);

        return $this->returnArray($result);
    }

    /**
     * 获取在 relationship 中使用的 article
     *
     * @param  array $articleIds
     * @return array
     */
    public function getInRelationship(array $articleIds): array
    {
        $articles = array_combine($articleIds, array_fill(0, count($articleIds), []));

        return $this->model
            ->field(['article_id', 'title', 'create_time', 'update_time'])
            ->fetchCollection()
            ->select($articleIds)
            ->keyBy('article_id')
            ->union($articles)
            ->all();
    }
}
