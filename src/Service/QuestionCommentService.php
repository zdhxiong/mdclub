<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\CommentableAbstracts;

/**
 * 对问题的评论
 *
 * Class QuestionCommentService
 * @package App\Service
 */
class QuestionCommentService extends CommentableAbstracts
{
    /**
     * 评论类型
     *
     * @var string
     */
    protected $commentableType = 'question';
}
