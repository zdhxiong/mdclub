<?php

declare(strict_types=1);

namespace MDClub\Facade\Transformer;

use MDClub\Initializer\Facade;
use MDClub\Transformer\Article;

/**
 * ArticleTransformer Facade
 *
 * @method static array transform(array $items, array $knownRelationship = [])
 * @method static array getInRelationship(array $articleIds)
 */
class ArticleTransformer extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Article::class;
    }
}
