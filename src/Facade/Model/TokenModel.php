<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Token;

/**
 * TokenModel Facade
 */
class TokenModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Token::class;
    }
}
