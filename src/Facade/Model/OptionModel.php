<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\Option;

/**
 * OptionModel Facade
 */
class OptionModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Option::class;
    }
}
