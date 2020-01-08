<?php

declare(strict_types=1);

namespace MDClub\Facade\Transformer;

use MDClub\Initializer\Facade;
use MDClub\Transformer\Answer;

/**
 * AnswerTransformer Facade
 *
 * @method static void  setInclude(array $includes)
 * @method static array transform(array $items, array $knownRelationship = [])
 * @method static array getInRelationship(array $answerIds)
 */
class AnswerTransformer extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Answer::class;
    }
}
