<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\Option;

/**
 * OptionValidator Facade
 *
 * @method static array update(array $data)
 */
class OptionValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Option::class;
    }
}
