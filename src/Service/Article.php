<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\Abstracts\Post;

/**
 * 文章
 *
 * @property-read \App\Model\Article      currentModel
 *
 * Class Article
 * @package App\Service
 */
class Article extends Post
{
    protected function getPrimaryKey(): string
    {
        return 'article_id';
    }

    protected function getTableName(): string
    {
        return 'article';
    }
}
