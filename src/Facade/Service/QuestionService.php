<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Facade\Service\Traits\Commentable;
use MDClub\Facade\Service\Traits\Followable;
use MDClub\Facade\Service\Traits\Getable;
use MDClub\Facade\Service\Traits\Votable;
use MDClub\Initializer\Facade;
use MDClub\Service\Question;

/**
 * QuestionService Facade
 *
 * @method static array getByUserId(int $userId)
 * @method static array getByTopicId(int $topicId)
 * @method static int   create(array $data)
 * @method static void  update(int $questionId, array $data)
 */
class QuestionService extends Facade
{
    use Commentable;
    use Followable;
    use Getable;
    use Votable;

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Question::class;
    }
}
