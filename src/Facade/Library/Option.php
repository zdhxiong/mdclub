<?php

declare(strict_types=1);

namespace MDClub\Facade\Library;

use MDClub\Initializer\Facade;

/**
 * Option Facade
 *
 * @method static \MDClub\Library\Option onlyAuthorized()
 * @method static mixed                  get(string $name)
 * @method static array                  getAll()
 * @method static void                   setMultiple(array $data)
 *
 * @ bool $answer_can_delete
 */
class Option extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return \MDClub\Library\Option::class;
    }
}
