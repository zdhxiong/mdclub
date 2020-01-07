<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\Facade;

/**
 * Throttle Facade
 *
 * @method static int getActLimit(string $id, string $action, int $maxCount, int $period)
 */
class Throttle extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \MDClub\Library\Throttle::class;
    }
}
