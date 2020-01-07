<?php

declare(strict_types=1);

namespace MDClub\Facade\Validator;

use MDClub\Initializer\Facade;
use MDClub\Validator\Topic;

/**
 * TopicValidator Facade
 *
 * @method static string getLastCover()
 * @method static array  create(array $data)
 * @method static array  update(int $topicId, array $data)
 */
class TopicValidator extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Topic::class;
    }
}
