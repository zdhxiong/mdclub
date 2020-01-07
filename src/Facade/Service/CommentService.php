<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Facade\Service\Traits\Getable;
use MDClub\Facade\Service\Traits\Votable;
use MDClub\Initializer\Facade;
use MDClub\Service\Comment;

/**
 * CommentService Facade
 *
 * @method static array getByUserId(int $userId)
 * @method static array getTrashed()
 * @method static void  update(int $commentId, array $data)
 * @method static void  delete(int $commentId)
 * @method static void  afterDelete(array $comments, bool $callByParent = false)
 */
class CommentService extends Facade
{
    use Getable;
    use Votable;

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Comment::class;
    }
}
