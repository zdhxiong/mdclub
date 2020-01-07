<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\User;

/**
 * UserValidator Facade
 *
 * @method static array sendRegisterEmail(array $data)
 * @method static array sendPasswordResetEmail(array $data)
 * @method static array register(array $data)
 * @method static array updatePassword(array $data)
 * @method static array update(int $userId, array $data)
 */
class UserValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return User::class;
    }
}
