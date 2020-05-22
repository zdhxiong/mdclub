<?php

declare(strict_types=1);

namespace MDClub\Facade\Transformer;

use MDClub\Initializer\Facade;
use MDClub\Transformer\Question;

/**
 * QuestionTransformer Facade
 *
 * @method static void  setInclude(array $includes)
 * @method static array getAvailableIncludes()
 * @method static array transform(array $items, array $knownRelationship = [], bool $canWithRelationships = true)
 * @method static array getInRelationship(array $questionIds)
 */
class QuestionTransformer extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Question::class;
    }
}
