<?php

declare(strict_types=1);

namespace MDClub\Transformer;

use MDClub\Facade\Model\ArticleModel;

/**
 * 文章转换器
 */
class Article extends Abstracts
{
    protected $table = 'article';
    protected $primaryKey = 'article_id';
    protected $availableIncludes = ['user', 'topics', 'is_following', 'voting'];
    protected $userExcept = ['delete_time'];

    /**
     * 格式化文章信息
     *
     * @param array $item
     * @return array
     */
    protected function format(array $item): array
    {
        if (isset($item['title'])) {
            $item['title'] = htmlentities($item['title']);
        }

        return $item;
    }

    /**
     * 获取 article 子资源
     *
     * @param  array $articleIds
     * @return array
     */
    public function getInRelationship(array $articleIds): array
    {
        if (!$articleIds) {
            return [];
        }

        $articles = ArticleModel::field(['article_id', 'title', 'create_time', 'update_time'])
            ->select($articleIds);

        return collect($articles)
            ->keyBy('article_id')
            ->map(function ($item) {
                $item['title'] = htmlentities($item['title']);

                return $item;
            })
            ->unionFill($articleIds)
            ->all();
    }
}
