<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\Answer;

/**
 * AnswerValidator Facade
 *
 * @method static array create(int $questionId, array $data)
 * @method static array update(int $answerId, array $data)
 * @method static array delete(int $answerId)
 */
class AnswerValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Answer::class;
    }
}
