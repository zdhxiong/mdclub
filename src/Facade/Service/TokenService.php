<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Initializer\Facade;
use MDClub\Service\Token;

/**
 * TokenService Facade
 *
 * @method static string create(array $data)
 */
class TokenService extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Token::class;
    }
}
