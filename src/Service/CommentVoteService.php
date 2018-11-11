<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\VotableAbstracts;

/**
 * 评论投票
 *
 * Class CommentVoteService
 * @package App\Service
 */
class CommentVoteService extends VotableAbstracts
{
    /**
     * 投票类型
     *
     * @var string
     */
    protected $votableType = 'comment';
}
