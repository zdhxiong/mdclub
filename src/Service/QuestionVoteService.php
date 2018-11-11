<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\VotableAbstracts;

/**
 * 问题投票
 *
 * Class QuestionVoteService
 * @package App\Service
 */
class QuestionVoteService extends VotableAbstracts
{
    /**
     * 投票类型
     *
     * @var string
     */
    protected $votableType = 'question';
}
