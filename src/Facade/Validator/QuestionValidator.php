<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\Question;

/**
 * QuestionValidator Facade
 *
 * @method static array create(array $data)
 * @method static array update(int $questionId, array $data)
 * @method static array delete(int $questionId)
 */
class QuestionValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Question::class;
    }
}
