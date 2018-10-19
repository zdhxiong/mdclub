<?php

declare(strict_types=1);

namespace App\Service;

use App\Abstracts\CommentAbstracts;
use App\Interfaces\CommentableInterface;

/**
 * 对问题的评论
 *
 * Class QuestionCommentService
 * @package App\Service
 */
class QuestionCommentService extends CommentAbstracts implements CommentableInterface
{

}
