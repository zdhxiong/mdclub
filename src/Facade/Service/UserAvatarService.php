<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Initializer\Facade;
use MDClub\Service\UserAvatar;

/**
 * UserAvatarService Facade
 *
 * @method static array  getBrandUrls(int $id, string $filename = null)
 * @method static string upload(int $userId, array $data)
 * @method static string delete(int $userId)
 */
class UserAvatarService extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return UserAvatar::class;
    }
}
