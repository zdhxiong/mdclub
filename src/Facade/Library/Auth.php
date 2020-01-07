<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\Facade;

/**
 * Auth Facade
 *
 * @property-read int         $lifeTime
 *
 * @method static int         getLifeTime()
 * @method static string|null getToken()
 * @method static void        setToken(string $token)
 * @method static array|null  getTokenInfo()
 * @method static int|null    userId()
 * @method static bool        isManager()
 * @method static bool        isNotManager()
 */
class Auth extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \MDClub\Library\Auth::class;
    }
}
