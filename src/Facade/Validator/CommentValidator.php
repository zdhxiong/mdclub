<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\Comment;

/**
 * CommentValidator Facade
 *
 * @method static array create(string $commentableType, int $commentableId, array $data)
 * @method static array update(int $commentId, array $data)
 * @method static array delete(int $commentId)
 */
class CommentValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Comment::class;
    }
}
