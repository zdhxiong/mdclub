<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\VotableAbstracts;

/**
 * 文章投票
 *
 * Class ArticleVoteService
 * @package App\Service
 */
class ArticleVoteService extends VotableAbstracts
{
    /**
     * 投票类型
     *
     * @var string
     */
    protected $votableType = 'article';
}
