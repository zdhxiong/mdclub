<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\Facade;

/**
 * Captcha Facade
 *
 * @method static array generate(int $width = 100, int $height = 36)
 * @method static bool  check(string $token, string $code)
 * @method static bool  isNextTimeNeed(string $id, string $action, int $max_count, int $period)
 */
class Captcha extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \MDClub\Library\Captcha::class;
    }
}
