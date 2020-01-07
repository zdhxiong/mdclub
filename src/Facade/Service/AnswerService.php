<?php

declare(strict_types=1);

namespace MDClub\Facade\Service;

use MDClub\Facade\Service\Traits\Commentable;
use MDClub\Facade\Service\Traits\Getable;
use MDClub\Facade\Service\Traits\Votable;
use MDClub\Initializer\Facade;
use MDClub\Service\Answer;

/**
 * AnswerService Facade
 *
 * @method static array getByUserId(int $userId)
 * @method static array getByQuestionId(int $questionId)
 * @method static array getTrashed()
 * @method static int   create(int $questionId, array $data)
 * @method static void  update(int $answerId, array $data)
 * @method static void  delete(int $answerId)
 * @method static void  afterDelete(array $answers, bool $callByParent = false)
 */
class AnswerService extends Facade
{
    use Commentable;
    use Getable;
    use Votable;

    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return Answer::class;
    }
}
