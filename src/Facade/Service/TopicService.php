<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Facade\Service\Traits\Brandable;
use MDClub\Facade\Service\Traits\Followable;
use MDClub\Facade\Service\Traits\Getable;
use MDClub\Initializer\Facade;
use MDClub\Service\Topic;

/**
 * TopicService Facade
 *
 * @method static array getTrashed()
 * @method static int   create(array $data)
 * @method static void  update(int $topicId, array $data)
 */
class TopicService extends Facade
{
    use Brandable;
    use Followable;
    use Getable;

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Topic::class;
    }
}
