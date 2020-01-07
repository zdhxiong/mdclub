<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\Token;

/**
 * TokenValidator Facade
 *
 * @method static int create(array $data)
 */
class TokenValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Token::class;
    }
}
