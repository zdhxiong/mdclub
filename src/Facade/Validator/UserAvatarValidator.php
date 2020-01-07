<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\UserAvatar;

/**
 * UserAvatarValidator Facade
 *
 * @method static array upload(array $data)
 */
class UserAvatarValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return UserAvatar::class;
    }
}
