<?php

declare(strict_types=1);

namespace MDClub\Facade\Model;

use MDClub\Model\User;

/**
 * UserModel Facade
 *
 * @method static array getList()
 * @method static array getDisabled()
 * @method static void  incAnswerCount(int $userId, int $count = 1)
 * @method static void  decAnswerCount(int $userId, int $count = 1)
 * @method static void  incArticleCount(int $userId, int $count = 1)
 * @method static void  decArticleCount(int $userId, int $count = 1)
 * @method static void  incQuestionCount(int $userId, int $count = 1)
 * @method static void  decQuestionCount(int $userId, int $count = 1)
 * @method static void  decFollowingTopicCount(int $userId, int $count = 1)
 */
class UserModel extends Abstracts
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return User::class;
    }
}
