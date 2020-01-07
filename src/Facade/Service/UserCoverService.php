<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Initializer\Facade;
use MDClub\Service\UserCover;

/**
 * UserCoverService Facade
 *
 * @method static array  getBrandUrls(int $id, string $filename = null)
 * @method static string upload(int $userId, array $data)
 * @method static void   delete(int $userId)
 */
class UserCoverService extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return UserCover::class;
    }
}
