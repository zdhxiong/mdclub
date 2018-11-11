<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\VotableAbstracts;

/**
 * 回答投票
 *
 * Class AnswerVoteService
 * @package App\Service
 */
class AnswerVoteService extends VotableAbstracts
{
    /**
     * 投票类型
     *
     * @var string
     */
    protected $votableType = 'answer';
}
