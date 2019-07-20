<?php

declare(strict_types=1);

namespace MDClub\Transformer;

/**
 * 文章转换器
 *
 * @property-read \MDClub\Model\Article $articleModel
 */
class Article extends Abstracts
{
    protected $table = 'article';
    protected $primaryKey = 'article_id';
    protected $availableIncludes = ['user', 'topics', 'is_following', 'voting'];
    protected $userExcept = ['delete_time'];

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

        $articles = $this->articleModel
            ->field(['article_id', 'title', 'create_time', 'update_time'])
            ->select($articleIds);

        return collect($articles)
            ->keyBy('article_id')
            ->unionFill($articleIds)
            ->all();
    }
}
