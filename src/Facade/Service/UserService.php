<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Facade\Service\Traits\Followable;
use MDClub\Facade\Service\Traits\Getable;
use MDClub\Initializer\Facade;
use MDClub\Service\User;

/**
 * UserService Facade
 *
 * @method static array getDisabled()
 * @method static void  enable(int $userId)
 * @method static void  enableMultiple(array $userIds)
 * @method static void  disable(int $userId)
 * @method static void  disableMultiple(array $userIds)
 * @method static void  sendRegisterEmail(array $data)
 * @method static void  sendPasswordResetEmail(array $data)
 * @method static array register(array $data)
 * @method static void  updatePassword(array $data)
 * @method static void  update(int $userId, array $data)
 */
class UserService extends Facade
{
    use Followable;
    use Getable;

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return User::class;
    }
}
