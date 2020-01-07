<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\Email;

/**
 * EmailValidator Facade
 *
 * @method static array send(array $data)
 */
class EmailValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Email::class;
    }
}
