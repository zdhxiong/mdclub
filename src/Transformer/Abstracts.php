<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Transformer\ArticleTransformer;
use MDClub\Facade\Transformer\FollowTransformer;
use MDClub\Facade\Transformer\QuestionTransformer;
use MDClub\Facade\Transformer\TopicTransformer;
use MDClub\Facade\Transformer\UserTransformer;
use MDClub\Facade\Transformer\VoteTransformer;
use MDClub\Helper\Str;

/**
 * 转换器抽象类
 */
abstract class Abstracts
{
    /**
     * @var string 表名
     */
    protected $table;

    /**
     * @var string 主键列名
     */
    protected $primaryKey;

    /**
     * @var array 可返回的子资源
     */
    protected $availableIncludes = [];

    /**
     * @var array 根据url参数，需要返回的子资源
     */
    protected $includes = [];

    /**
     * @var array 普通用户需移除的字段
     */
    protected $userExcept = [];

    /**
     * @var array 管理员需移除的字段
     */
    protected $managerExcept = [];

    public function __construct()
    {
        $includes = Request::getQueryParams()['include'] ?? '';
        $includes = explode(',', $includes);
        $this->includes = array_intersect($this->availableIncludes, $includes);
    }

    /**
     * 数据格式化
     *
     * @param  array $item
     * @return array
     */
    protected function format(array $item): array
    {
        return $item;
    }

    /**
     * 添加 user 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function user(array $items): array
    {
        $userIds = array_unique(array_column($items, 'user_id'));
        $users = UserTransformer::getInRelationship($userIds);

        foreach ($items as &$item) {
            if (isset($item['user_id'])) {
                $item['relationships']['user'] = $users[$item['user_id']];
            }
        }

        return $items;
    }

    /**
     * 添加 question 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function question(array $items): array
    {
        $questionIds = array_unique(array_column($items, 'question_id'));
        $questions = QuestionTransformer::getInRelationship($questionIds);

        foreach ($items as &$item) {
            if (isset($item['question_id'])) {
                $item['relationships']['question'] = $questions[$item['question_id']];
            }
        }

        return $items;
    }

    /**
     * 获取 article 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function article(array $items): array
    {
        $articleIds = array_unique(array_column($items, 'article_id'));
        $articles = ArticleTransformer::getInRelationship($articleIds);

        foreach ($items as &$item) {
            if (isset($item['article_id'])) {
                $item['relationships']['article'] = $articles[$item['article_id']];
            }
        }

        return $items;
    }

    /**
     * 添加投票状态
     *
     * @param  array $items
     * @return array
     */
    protected function voting(array $items): array
    {
        $keys = array_unique(array_column($items, $this->primaryKey));
        $votings = VoteTransformer::getInRelationship($keys, $this->table);

        foreach ($items as &$item) {
            if (isset($item[$this->primaryKey])) {
                $item['relationships']['voting'] = $votings[$item[$this->primaryKey]];
            }
        }

        return $items;
    }

    /**
     * 添加 topics 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function topics(array $items): array
    {
        $keys = array_unique(array_column($items, $this->primaryKey));
        $topics = TopicTransformer::getInRelationship($keys, $this->table);

        foreach ($items as &$item) {
            if (isset($item[$this->primaryKey])) {
                $item['relationships']['topics'] = $topics[$item[$this->primaryKey]];
            }
        }

        return $items;
    }

    /**
     * 添加 is_following 状态
     *
     * @param  array $items
     * @param  array $knownRelationship ['is_following' => true]
     * @return array
     */
    protected function isFollowing(array $items, array $knownRelationship): array
    {
        $keys = array_unique(array_column($items, $this->primaryKey));

        if (isset($knownRelationship['is_following'])) {
            $followingKeys = $knownRelationship['is_following'] ? $keys : [];
        } else {
            $followingKeys = FollowTransformer::getInRelationship($keys, $this->table);
        }

        foreach ($items as &$item) {
            if (isset($item[$this->primaryKey])) {
                $item['relationships']['is_following'] = in_array($item[$this->primaryKey], $followingKeys, true);
            }
        }

        return $items;
    }

    /**
     * 转换数据
     *
     * @param  array $items             数组，或多个元素组成的二维数组
     * @param  array $knownRelationship 已知的 relationship，若指定了该参数，则对于的字段不再需要计算
     * @return array
     */
    public function transform(array $items, array $knownRelationship = []): array
    {
        if (!$items) {
            return $items;
        }

        $isSingleItem = !isset($items[0]);

        if ($isSingleItem) {
            $items = [$items];
        }

        // 移除字段
        $except = Auth::isManager() ? $this->managerExcept : $this->userExcept;
        $items = collect($items)->exceptSpread($except);

        // 格式化
        $items = $items->map(function ($item) {
            return $this->format($item);
        })->all();

        // 添加 relationships
        foreach ($this->includes as $include) {
            $method = Str::toCamelize($include);

            if (method_exists($this, $method)) {
                $items = $this->{$method}($items, $knownRelationship);
            }
        }

        return $isSingleItem ? $items[0] : $items;
    }
}
