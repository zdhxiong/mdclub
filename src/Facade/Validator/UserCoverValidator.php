<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\UserCover;

/**
 * UserCoverValidator Facade
 *
 * @method static array upload(array $data)
 */
class UserCoverValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return UserCover::class;
    }
}
