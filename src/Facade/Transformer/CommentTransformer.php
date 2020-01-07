<?php

declare(strict_types=1);

namespace MDClub\Facade\Transformer;

use MDClub\Initializer\Facade;
use MDClub\Transformer\Comment;

/**
 * CommentTransformer Facade
 *
 * @method static array transform(array $items, array $knownRelationship = [])
 * @method static array getInRelationship(array $commentIds)
 */
class CommentTransformer extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Comment::class;
    }
}
