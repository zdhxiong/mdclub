<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Library\Auth;
use MDClub\Facade\Library\Request;
use MDClub\Facade\Transformer\FollowTransformer;
use MDClub\Facade\Transformer\TopicTransformer;
use MDClub\Facade\Transformer\VoteTransformer;
use MDClub\Helper\Str;
use MDClub\Initializer\App;

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
        $this->setInclude($includes);
    }

    /**
     * 设置 include
     *
     * @param array $includes
     */
    public function setInclude(array $includes): void
    {
        $this->includes = array_intersect($this->availableIncludes, $includes);
    }

    /**
     * 获取支持的 includes 参数数组
     *
     * @return array
     */
    public function getAvailableIncludes(): array
    {
        return $this->availableIncludes;
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
     * 获取用在 relationships 中的子资源
     * 例如根据 user_id，生产用在 relationships 中的 user
     *
     * @param array  $items
     * @param string $name relationships 中的字段名，需要有对应的以 _id 为后缀的字段
     * @param string $transformerClass
     *
     * @return array
     */
    protected function relationshipItemTransform(array $items, string $name, string $transformerClass): array
    {
        $idName = "${name}_id";
        $ids = collect($items)->pluck($idName)->unique()->filter()->all();
        $transformer = App::$container->get($transformerClass);
        $children =  $transformer->getInRelationship($ids);

        foreach ($items as &$item) {
            if (isset($item[$idName])) {
                $item['relationships'][$name] = $item[$idName] ? $children[$item[$idName]] : null;
            }
        }

        return $items;
    }

    /**
     * 添加 user 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function user(array $items): array
    {
        return $this->relationshipItemTransform($items, 'user', User::class);
    }

    /**
     * 添加 question 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function question(array $items): array
    {
        return $this->relationshipItemTransform($items, 'question', Question::class);
    }

    /**
     * 添加 answer 子资源
     *
     * @param array $items
     * @return array
     */
    protected function answer(array $items): array
    {
        return $this->relationshipItemTransform($items, 'answer', Answer::class);
    }

    /**
     * 获取 article 子资源
     *
     * @param  array $items
     * @return array
     */
    protected function article(array $items): array
    {
        return $this->relationshipItemTransform($items, 'article', Article::class);
    }

    /**
     * 获取 comment 子资源
     *
     * @param array $items
     * @return array
     */
    protected function comment(array $items): array
    {
        return $this->relationshipItemTransform($items, 'comment', Comment::class);
    }

    /**
     * 添加投票状态
     *
     * @param  array $items
     * @return array
     */
    protected function voting(array $items): array
    {
        $keys = collect($items)->pluck($this->primaryKey)->unique()->filter()->all();
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
        $keys = collect($items)->pluck($this->primaryKey)->unique()->filter()->all();
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
        $keys = collect($items)->pluck($this->primaryKey)->unique()->filter()->all();

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
     * @param  array $knownRelationship 已知的 relationship，若指定了该参数，则对应的字段不再需要计算
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
