<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Article;

/**
 * ArticleModel Facade
 *
 * @method static array getByUserId(int $userId)
 * @method static array getByTopicId(int $topicId)
 * @method static array getList()
 * @method static void  decCommentCount(int $articleId, int $count = 1)
 */
class ArticleModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Article::class;
    }
}
