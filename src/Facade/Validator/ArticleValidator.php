<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\Article;

/**
 * ArticleValidator Facade
 *
 * @method static array create(array $data)
 * @method static array update(int $articleId, array $data)
 * @method static array delete(int $articleId)
 */
class ArticleValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Article::class;
    }
}
